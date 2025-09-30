$(function () {
  const hasSolicitacoes = Math.random() > 0.4;
  const PAGE_SIZE = 5;
  let currentPage = 1;

  const solicitacoesMock = hasSolicitacoes
    ? [
        {
          numero: "5902938",
          data: "01/03/2017",
          servico: "Bombeiro Hidráulico - Vazamento em descarga",
          status: "aguardando",
        },
        {
          numero: "8273028",
          data: "20/01/2017",
          servico: "Carpintaria - Reparo em armário",
          status: "execucao",
        },
        {
          numero: "8273028",
          data: "20/01/2017",
          servico: "Eletricista - Interruptor não funciona",
          status: "pendente",
        },
        {
          numero: "9139482",
          data: "30/10/2016",
          servico: "Chaveiro - Abrir porta por perda de chave",
          status: "concluido",
        },
        {
          numero: "7629382",
          data: "13/09/2015",
          servico: "Bombeiro Hidráulico - Vazamento no teto",
          status: "cancelado",
        },
      ]
    : [];
  const solicitacoesLocal = JSON.parse(localStorage.getItem("solicitacoes") || "[]");

  const solicitacoes = [...solicitacoesMock, ...solicitacoesLocal];

  function renderTable() {
    const container = $("#lista-container");
    container.empty();

    if (solicitacoes.length === 0) {
      container.append(`<div class="no-solicitacoes">Você não tem solicitações no momento</div>`);
      $("#pagination").hide();
      return;
    }

    const start = (currentPage - 1) * PAGE_SIZE;
    const end = start + PAGE_SIZE;
    const pageData = solicitacoes.slice(start, end);

    let table = `
      <table>
        <thead>
          <tr>
            <th>Número</th>
            <th>Data</th>
            <th>Serviço</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
    `;

    pageData.forEach((s) => {
      let statusText = "";
      switch (s.status) {
        case "aguardando":
          statusText = `<span class="status-badge status-aguardando">Aguardando início</span>`;
          break;
        case "execucao":
          statusText = `<span class="status-badge status-execucao">Em execução</span>`;
          break;
        case "pendente":
          statusText = `<span class="status-badge status-pendente">Pendente</span>`;
          break;
        case "concluido":
          statusText = `
            <span class="status-badge status-concluido">Concluído</span>
            <a href="avaliar.html?num=${s.numero}" class="status-link">Avaliar</a>
          `;
          break;
        case "cancelado":
          statusText = `<span class="status-badge status-cancelado">Cancelado</span>`;
          break;
        default:
          statusText = `<span>${s.status}</span>`;
      }

      table += `
        <tr>
          <td>${s.numero}</td>
          <td>${s.data}</td>
          <td>${s.servico || s.tipo + " - " + s.descricao}</td>
          <td>${statusText}</td>
        </tr>
      `;
    });

    table += `</tbody></table>`;
    container.append(table);

    renderPagination();
  }

  function renderPagination() {
    const totalPages = Math.ceil(solicitacoes.length / PAGE_SIZE);
    const pagination = $("#pagination");
    pagination.empty();

    if (totalPages <= 1) {
      pagination.hide();
      return;
    }

    pagination.show();
    pagination.append(`
      <button id="prevPage" ${currentPage === 1 ? "disabled" : ""}>Anterior</button>
      <span>Página ${currentPage} de ${totalPages}</span>
      <button id="nextPage" ${currentPage === totalPages ? "disabled" : ""}>Próxima</button>
    `);

    $("#prevPage").on("click", () => {
      if (currentPage > 1) {
        currentPage--;
        renderTable();
      }
    });

    $("#nextPage").on("click", () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderTable();
      }
    });
  }

  $("#btnAdd").on("click", function () {
    window.location.href = "solicitar_servico.html";
  });

  renderTable();
});
