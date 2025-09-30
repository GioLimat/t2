$(function () {
  $("#formLogin").on("submit", function (e) {
    e.preventDefault();

    const email = $("#loginEmail").val().trim();
    const password = $("#loginPassword").val();

    const users = JSON.parse(localStorage.getItem("users") || "[]");
    const user = users.find((u) => u.email === email && u.password === password);

    if (!user) {
      alert("Email ou senha inválidos!");
      return;
    }

    localStorage.setItem("loggedUser", JSON.stringify(user));

    alert(`Bem-vindo, ${user.name}!`);

    if (user.role === "prestador") {
      window.location.href = "painel_prestador.html";
    } else {
      window.location.href = "lista_solicitacoes.html";
    }
  });
});
