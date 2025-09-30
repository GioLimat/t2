$(function () {
  const categorias = {
    "Bombeiro Hidráulico": ["Vazamento em Torneira", "Vazamento em Descarga", "Vazamento no teto"],
    Eletricista: ["Interruptor não funciona", "Troca de lâmpada", "Curto circuito"],
    Chaveiro: ["Abrir porta por perda de chave", "Troca de fechadura"],
    Marceneiro: ["Reparo em armário", "Montagem de móveis"],
  };

  const $tipo = $("#tipoServico");
  const $servico = $("#servico");

  Object.keys(categorias).forEach((cat) => {
    $tipo.append(`<option value="${cat}">${cat}</option>`);
  });

  function atualizarServicos() {
    const selecionado = $tipo.val();
    $servico.empty();
    categorias[selecionado].forEach((s) => {
      $servico.append(`<option value="${s}">${s}</option>`);
    });
  }

  $tipo.on("change", atualizarServicos);
  atualizarServicos();

  $("#formSolicitar").on("submit", function (e) {
    e.preventDefault();

    const tipo = $tipo.val();
    const servico = $servico.val();
    const descricao = $("#descricao").val();

    const user = JSON.parse(localStorage.getItem("loggedUser") || "null");
    if (!user) {
      alert("Você precisa estar logado para criar uma solicitação!");
      window.location.href = "login.html";
      return;
    }

    const precos = {
      "Vazamento em Torneira": 150,
      "Vazamento em Descarga": 200,
      "Vazamento no teto": 300,
      "Interruptor não funciona": 120,
      "Troca de lâmpada": 80,
      "Curto circuito": 250,
      "Abrir porta por perda de chave": 180,
      "Troca de fechadura": 220,
      "Reparo em armário": 200,
      "Montagem de móveis": 250,
    };

    const precoBase = precos[servico] || 100;
    const credito = 10;
    const valorFinal = precoBase - credito;

    // Preenche dados na tela de confirmação de preço
    $("#precoTipo").text(tipo);
    $("#precoServico").text(servico);
    $("#precoValor").text(`R$ ${precoBase.toFixed(2)}`);
    $("#precoCredito").text(`R$ ${credito.toFixed(2)}`);
    $("#precoCobrado").text(`R$ ${valorFinal.toFixed(2)}`);

    $("#formSolicitar").hide();
    $("#confirmarPreco").show();

    $("#cancelarPreco")
      .off("click")
      .on("click", function () {
        $("#confirmarPreco").hide();
        $("#formSolicitar").show();
      });

    $("#confirmarPrecoBtn")
      .off("click")
      .on("click", function () {
        const numero = Date.now().toString().slice(-7);
        const nova = {
          numero,
          data: new Date().toLocaleDateString(),
          tipo,
          servico,
          descricao,
          status: "aguardando",
        };

        let solicitacoes = JSON.parse(localStorage.getItem("solicitacoes") || "[]");
        solicitacoes.push(nova);
        localStorage.setItem("solicitacoes", JSON.stringify(solicitacoes));

        const prestadoresFake = ["ana@provedor.com", "joao@email.com", "artur@gmail.com"];
        const prestador = prestadoresFake[Math.floor(Math.random() * prestadoresFake.length)];

        $("#confirmarPreco").hide();
        $("#confNumero").text(numero);
        $("#confPrestador").text(prestador);
        $("#confirmacao").show();
      });
  });
});
