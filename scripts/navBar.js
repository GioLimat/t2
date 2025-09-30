$(function () {
  const user = JSON.parse(localStorage.getItem("loggedUser") || "null");

  let navHtml = `
    <div class="brand">
      <div class="logo">SF</div>
      <a href="index.html">Serviço Fácil</a>
    </div>
    <nav class="nav">
  `;

  if (!user) {
    navHtml += `
      <a class="btn ghost" href="cadastro.html">Cadastro</a>
      <a class="btn ghost" href="login.html">Login</a>
    `;
  } else {
    if (user.role === "cliente") {
      navHtml += `
        <a class="btn" href="solicitar_servico.html">Solicitar Serviço</a>
        <a class="btn ghost" href="lista_solicitacoes.html">Minhas Solicitações</a>
      `;
    } else if (user.role === "prestador") {
      navHtml += `
        <a class="btn" href="painel_prestador.html">Painel Prestador</a>
      `;
    }
    navHtml += `
      <span style="font-weight:600; color:var(--purple-700); margin-left:10px;">
        Olá, ${user.name}
      </span>
      <button id="logoutBtn" class="btn ghost">Sair</button>
    `;
  }

  navHtml += `</nav>`;

  $("#navbar").html(navHtml);

  $("#logoutBtn").on("click", function () {
    localStorage.removeItem("loggedUser");
    window.location.href = "index.html";
  });
});
