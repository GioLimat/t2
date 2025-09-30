$(function () {
  $("#formRegister").on("submit", function (e) {
    e.preventDefault();

    const name = $("#registerName").val().trim();
    const email = $("#registerEmail").val().trim();
    const password = $("#registerPassword").val();
    const confirmPassword = $("#registerConfirmPassword").val();
    const role = $("#registerRole").val();

    if (password !== confirmPassword) {
      alert("As senhas não coincidem!");
      return;
    }

    if (!role) {
      alert("Selecione o tipo de conta!");
      return;
    }

    const users = JSON.parse(localStorage.getItem("users") || "[]");
    if (users.find((u) => u.email === email)) {
      alert("Já existe uma conta com esse email.");
      return;
    }

    const novoUser = { name, email, password, role };
    users.push(novoUser);
    localStorage.setItem("users", JSON.stringify(users));

    localStorage.setItem("loggedUser", JSON.stringify(novoUser));
    $("#formRegister").hide();
    $("#confirmacaoCadastro").show();
  });

  $(document).on("click", "#btnConfirmarCadastro", function () {
    const user = JSON.parse(localStorage.getItem("loggedUser") || "null");
    if (!user) {
      window.location.href = "login.html";
      return;
    }

    if (user.role === "cliente") {
      window.location.href = "solicitar_servico.html";
    } else {
      window.location.href = "painel_prestador.html";
    }
  });
});
