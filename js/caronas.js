document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();
    carregarCaronas();
});

document.getElementById("btnNovaCarona").addEventListener("click", () => {
    window.location.href = "caronas_nova.html";
});

document.getElementById("btnLogoff").addEventListener("click", async () => {
    const retorno = await fetch("../php/cliente_logoff.php");
    const resposta = await retorno.json();
    if (resposta.status == "ok") {
        window.location.href = "../login/";
    }
});

document.getElementById("btnExcluirConta").addEventListener("click", async () => {
    if (confirm("Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.")) {
        const retorno = await fetch("../php/conta_excluir.php");
        const resposta = await retorno.json();
        if (resposta.status === "ok") {
            alert("Conta excluída com sucesso.");
            window.location.href = "../login/";
        } else {
            alert("Erro: " + resposta.mensagem);
        }
    }
});

document.getElementById("buscar").addEventListener("click", () => {
    const origem = document.getElementById("origem").value.trim();
    const destino = document.getElementById("destino").value.trim();
    buscarCaronas(origem, destino);
});

document.getElementById("limpar").addEventListener("click", () => {
    document.getElementById("origem").value = "";
    document.getElementById("destino").value = "";
    carregarCaronas();
});

async function carregarCaronas(){
    const retorno = await fetch("../php/caronas_get.php");
    const resposta = await retorno.json();
    if (resposta.status === "ok") {
        preencherTabela(resposta.data);
    } else {
        document.getElementById("lista").innerHTML = "<p>Nenhuma carona disponível</p>";
    }
}

async function buscarCaronas(origem, destino){
    let url = "../php/caronas_search.php";
    const params = new URLSearchParams();
    if (origem) params.append("origem", origem);
    if (destino) params.append("destino", destino);
    if([...params].length > 0) url += "?" + params.toString();

    const retorno = await fetch(url);
    const resposta = await retorno.json();
    if (resposta.status === "ok") {
        preencherTabela(resposta.data);
    } else {
        document.getElementById("lista").innerHTML = "<p>Nenhuma carona encontrada</p>";
    }
}

function preencherTabela(lista) {
    if (!Array.isArray(lista) || lista.length === 0) {
        document.getElementById("lista").innerHTML = "<p>Nenhuma carona disponível</p>";
        return;
    }

    let html = "<table border='1' cellpadding='8'><tr><th>Motorista</th><th>Origem</th><th>Destino</th><th>Data/Hora</th><th>Vagas</th><th>Valor</th><th>Detalhes</th></tr>";
    lista.forEach(item => {
        html += `<tr>
            <td>${item.motorista_nome}</td>
            <td>${item.origem}</td>
            <td>${item.destino}</td>
            <td>${item.data_hora}</td>
            <td>${item.vagas}</td>
            <td>${item.valor}</td>
            <td><a href='rota_detalhes.html?id=${item.id}'>Ver</a></td>
        </tr>`;
    });
    html += "</table>";
    document.getElementById("lista").innerHTML = html;
}