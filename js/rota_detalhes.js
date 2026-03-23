document.addEventListener("DOMContentLoaded", () => {
  valida_sessao();
  document.getElementById("voltar").addEventListener("click", () => window.location.href = "index.html");
  carregarDetalhes();
});

async function carregarDetalhes() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (!id) {
    alert("ID da carona não informado.");
    window.location.href = "index.html";
    return;
  }

  const retorno = await fetch(`../php/caronas_get.php?id=${id}`);
  const resposta = await retorno.json();
  if (resposta.status === "ok" && resposta.data && resposta.data.length > 0) {
    const item = resposta.data[0];
    const html = `
      <p><strong>Motorista:</strong> ${item.motorista_nome}</p>
      <p><strong>Origem:</strong> ${item.origem}</p>
      <p><strong>Destino:</strong> ${item.destino}</p>
      <p><strong>Data/Hora:</strong> ${item.data_hora}</p>
      <p><strong>Vagas:</strong> ${item.vagas}</p>
      <p><strong>Valor:</strong> R$ ${item.valor}</p>
      <p><strong>Descrição:</strong> ${item.descricao}</p>
    `;
    document.getElementById("detalhes").innerHTML = html;
  } else {
    document.getElementById("detalhes").innerHTML = "<p>Carona não encontrada.</p>";
  }
}
