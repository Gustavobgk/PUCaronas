document.addEventListener("DOMContentLoaded", () => {
  valida_sessao();
  document.getElementById("enviar").addEventListener("click", postarCarona);
  document.getElementById("voltar").addEventListener("click", () => window.location.href = "index.html");
});

async function postarCarona() {
  const origem = document.getElementById("origem").value.trim();
  const destino = document.getElementById("destino").value.trim();
  const data_hora = document.getElementById("data_hora").value;
  const vagas = parseInt(document.getElementById("vagas").value, 10);
  const valor = document.getElementById("valor").value;
  const descricao = document.getElementById("descricao").value.trim();

  if (!origem || !destino || !data_hora || !vagas || !valor) {
    alert("Preencha todos os campos obrigatórios.");
    return;
  }

  const fd = new FormData();
  fd.append("origem", origem);
  fd.append("destino", destino);
  fd.append("data_hora", data_hora);
  fd.append("vagas", vagas);
  fd.append("valor", valor);
  fd.append("descricao", descricao);

  const retorno = await fetch("../php/caronas_post.php", { method: "POST", body: fd });
  const resposta = await retorno.json();

  if (resposta.status === "ok") {
    alert("Carona publicada com sucesso!");
    window.location.href = "index.html";
  } else {
    alert("Erro: " + resposta.mensagem);
  }
}
