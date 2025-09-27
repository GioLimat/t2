import * as API from "./api.js";
import { q, formatMoney, go } from "./utils.js";

if (document.body.dataset.page === "cadastro") {
  const form = q("#formCadastro");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const name = q("#nome").value.trim();
    const email = q("#email").value.trim();
    if (!name || !email) return;
    API.createUser({ name, email, credit: 0 });
    API.saveCurrentUser(email);
    go("confirmacao_cadastro.html");
  });
}

if (document.body.dataset.page === "login") {
  const form = q("#formLogin");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const email = q("#loginEmail").value.trim();
    const user = API.findUserByEmail(email);
    if (user) {
      API.saveCurrentUser(email);
      go("solicitar_servico.html");
    } else {
      alert("Usuário não encontrado. Faça o cadastro.");
    }
  });
}

if (document.body.dataset.page === "solicitar") {
  const form = q("#formServico");
  const preview = q("#precoPreview");
  const table = q("#linksAll");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const type = q("#tipo").value;
    const desc = q("#desc").value;
    const base = {
      Encanamento: 120,
      Elétrica: 160,
      Montagem: 90,
      Pintura: 110,
    };
    const price = base[type] || 100;
    const temp = { serviceType: type, description: desc, price };
    localStorage.setItem("sf_temp_request", JSON.stringify(temp));
    go("confirmar_preco.html");
  });
}

if (document.body.dataset.page === "confirmar_preco") {
  const temp = JSON.parse(localStorage.getItem("sf_temp_request") || "null");
  const user = API.getCurrentUser();
  const elType = q("#tipoInfo");
  const elPrice = q("#precoInfo");
  if (temp) {
    elType.textContent = temp.serviceType;
    elPrice.textContent = formatMoney(temp.price);
  }
  const btnConfirm = q("#confirmBtn");
  btnConfirm.addEventListener("click", () => {
    const payload = {
      clientEmail: user?.email || "guest",
      serviceType: temp.serviceType,
      description: temp.description,
      price: temp.price,
    };
    const svc = API.allocateService(payload);
    localStorage.setItem("sf_last_service", svc.id);
    go("confirmacao_solicitacao.html");
  });
}

if (document.body.dataset.page === "confirmacao") {
  const last = localStorage.getItem("sf_last_service");
  const svc = API.getServices().find((s) => s.id === last) || null;
  if (svc) {
    q("#svcId").textContent = svc.id;
    q("#svcType").textContent = svc.serviceType;
    q("#svcPrice").textContent = formatMoney(svc.price);
    q("#svcProvider").textContent = svc.providerName;
  }
}

if (document.body.dataset.page === "lista") {
  const user = API.getCurrentUser();
  const list = q("#listaServicos");
  const arr = API.getServicesByClient(user?.email);
  if (arr.length === 0) list.innerHTML = '<div class="small">Sem solicitações</div>';
  else {
    list.innerHTML = "";
    arr.forEach((s) => {
      const div = document.createElement("div");
      div.className = "item";
      div.innerHTML = `<div class="meta">
        <div class="page-title">${s.serviceType} <span class="small">#${s.id}</span></div>
        <div class="small">Prestador: ${s.providerName} • ${new Date(
        s.createdAt
      ).toLocaleString()}</div>
      </div>
      <div class="kv">
        <div class="badge">${formatMoney(s.price)}</div>
        <div class="status ${
          s.status === "Em execução"
            ? "execucao"
            : s.status === "Concluído"
            ? "concluido"
            : s.status === "Pendente"
            ? "pendente"
            : "alocado"
        }">${s.status}</div>
        <a class="btn" href="avaliar_prestador.html?svc=${s.id}">Avaliar</a>
      </div>`;
      list.appendChild(div);
    });
  }
}

if (document.body.dataset.page === "avaliar") {
  const params = new URLSearchParams(location.search);
  const svcId = params.get("svc");
  const svc = API.getServices().find((s) => s.id === svcId);
  if (svc) {
    q("#svcInfo").textContent = `${svc.serviceType} • ${svc.providerName} • ${formatMoney(
      svc.price
    )}`;
  }
  const form = q("#formAvaliar");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const rating = Number(q('input[name="rating"]:checked').value);
    const text = q("#reviewText").value.trim();
    API.addRating(svcId, rating, text);
    alert("Avaliação enviada. Obrigado!");
    location.href = "lista_solicitacoes.html";
  });
}
