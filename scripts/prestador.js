import * as API from "./api.js";
import { q, formatMoney } from "./utils.js";

if (document.body.dataset.page === "painel") {
  const providers = API.getProviders();
  const listRoot = q("#painelList");
  listRoot.innerHTML = "";
  providers.forEach((p) => {
    const wrapper = document.createElement("div");
    wrapper.className = "card";
    const h = document.createElement("div");
    h.className = "page-title";
    h.textContent = p.name;
    wrapper.appendChild(h);
    const arr = API.getServicesByProvider(p.id);
    if (arr.length === 0) {
      const empty = document.createElement("div");
      empty.className = "small";
      empty.textContent = "Sem serviços alocados";
      wrapper.appendChild(empty);
    } else {
      const box = document.createElement("div");
      box.className = "list";
      arr.forEach((s) => {
        const it = document.createElement("div");
        it.className = "item";
        let stClass =
          s.status === "Em execução"
            ? "execucao"
            : s.status === "Concluído"
            ? "concluido"
            : s.status === "Pendente"
            ? "pendente"
            : "alocado";
        it.innerHTML = `<div class="meta">
            <div class="page-title">${s.serviceType} <span class="small">#${s.id}</span></div>
            <div class="small">${s.clientEmail} • ${new Date(s.createdAt).toLocaleString()}</div>
            <div class="small">${s.description || ""}</div>
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
            <div class="badge">${formatMoney(s.price)}</div>
            <div class="status ${stClass}">${s.status}</div>
            <div style="display:flex;gap:6px">
              <button class="btn start" data-id="${s.id}">Iniciar</button>
              <button class="btn ghost pend" data-id="${s.id}">Pendente</button>
              <button class="btn" data-id="${s.id}" style="background:#10b981">Concluir</button>
            </div>
          </div>`;
        box.appendChild(it);
      });
      wrapper.appendChild(box);
    }
    listRoot.appendChild(wrapper);
  });
  listRoot.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const id = btn.dataset.id;
    if (btn.classList.contains("start")) {
      const res = API.updateServiceStatus(id, "Em execução");
      if (res && res.error) alert(res.error);
      else location.reload();
    } else if (btn.classList.contains("pend")) {
      API.updateServiceStatus(id, "Pendente");
      location.reload();
    } else {
      API.updateServiceStatus(id, "Concluído");
      alert("Serviço marcado como Concluído. Crédito aplicado ao cliente.");
      location.reload();
    }
  });
}
