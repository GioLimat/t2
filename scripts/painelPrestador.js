$(function () {
  let servicos = [
    {
      numero: "1001",
      data: "02/10/2023",
      cliente: "maria@email.com",
      tipo: "Bombeiro Hidráulico",
      servico: "Vazamento em descarga",
      status: "novo",
    },
    {
      numero: "1002",
      data: "05/10/2023",
      cliente: "joao@email.com",
      tipo: "Eletricista",
      servico: "Interruptor não funciona",
      status: "execucao",
    },
    {
      numero: "1003",
      data: "08/10/2023",
      cliente: "ana@email.com",
      tipo: "Chaveiro",
      servico: "Troca de fechadura",
      status: "pendente",
    },
    {
      numero: "1004",
      data: "10/10/2023",
      cliente: "carlos@email.com",
      tipo: "Marceneiro",
      servico: "Montagem de móveis",
      status: "concluido",
      avaliacao: { nota: 5, comentario: "Excelente trabalho!" },
    },
  ];

  function renderSection(status, containerId) {
    const container = $(containerId);
    container.empty();

    const filtrados = servicos.filter((s) => s.status === status);

    if (filtrados.length === 0) {
      container.append(`<p>Nenhum serviço ${status}.</p>`);
      return;
    }

    let table = `
        <table>
          <thead>
            <tr>
              <th>Número</th>
              <th>Data</th>
              <th>Cliente</th>
              <th>Serviço</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
      `;

    filtrados.forEach((s) => {
      table += `
          <tr>
            <td>${s.numero}</td>
            <td>${s.data}</td>
            <td>${s.cliente}</td>
            <td>${s.tipo} - ${s.servico}</td>
            <td>
              <select data-numero="${s.numero}" class="status-select">
                <option value="novo" ${s.status === "novo" ? "selected" : ""}>Novo</option>
                <option value="execucao" ${
                  s.status === "execucao" ? "selected" : ""
                }>Em Execução</option>
                <option value="pendente" ${
                  s.status === "pendente" ? "selected" : ""
                }>Pendente</option>
                <option value="concluido" ${
                  s.status === "concluido" ? "selected" : ""
                }>Concluído</option>
                <option value="cancelado" ${
                  s.status === "cancelado" ? "selected" : ""
                }>Cancelado</option>
              </select>
              ${
                s.status === "concluido" && s.avaliacao
                  ? `
                <div><a href="avaliar.html?num=${s.numero}" class="avaliacoes-link">
                  Ver Avaliação (Nota: ${s.avaliacao.nota})
                </a></div>`
                  : ""
              }
            </td>
          </tr>
        `;
    });

    table += `</tbody></table>`;
    container.append(table);
  }

  function renderAll() {
    renderSection("novo", "#lista-novos");
    renderSection("execucao", "#lista-execucao");
    renderSection("pendente", "#lista-pendentes");
  }

  $(document).on("change", ".status-select", function () {
    const numero = $(this).data("numero");
    const novoStatus = $(this).val();
    const servico = servicos.find((s) => s.numero === numero);
    if (servico) {
      servico.status = novoStatus;

      if (novoStatus === "concluido") {
        servico.avaliacao = { nota: 4, comentario: "Muito bom!" };
      }
      renderAll();
    }
  });

  renderAll();
});
