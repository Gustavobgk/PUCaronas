document.addEventListener("DOMContentLoaded", () => {
    // Mostrar/esconder campos do motorista
    document.getElementById("tipo").addEventListener("change", (e) => {
        const camposMotorista = document.getElementById("campos_motorista");
        if (e.target.value === "motorista") {
            camposMotorista.style.display = "block";
        } else {
            camposMotorista.style.display = "none";
        }
    });
});

document.getElementById("enviar").addEventListener("click", async () => {
    const nome = document.getElementById("nome").value.trim();
    const email = document.getElementById("email").value.trim();
    const emailAcademico = document.getElementById("email_academico").value.trim();
    const idade = document.getElementById("idade").value.trim();
    const usuario = document.getElementById("usuario").value.trim();
    const senha = document.getElementById("senha").value.trim();
    const tipo = document.getElementById("tipo").value;

    if (!nome || !email || !emailAcademico || !idade || !usuario || !senha) {
        alert("Preencha todos os campos obrigatórios.");
        return;
    }

    // Validação básica do email acadêmico
    if (!emailAcademico.includes("@puc") && !emailAcademico.includes("@puc.br")) {
        alert("Email acadêmico deve ser da PUC (@puc ou @puc.br).");
        return;
    }

    const fd = new FormData();
    fd.append("nome", nome);
    fd.append("email", email);
    fd.append("email_academico", emailAcademico);
    fd.append("idade", idade);
    fd.append("usuario", usuario);
    fd.append("senha", senha);
    fd.append("tipo", tipo);

    // Campos específicos do motorista
    if (tipo === "motorista") {
        const fotoPerfil = document.getElementById("foto_perfil").files[0];
        const carro = document.getElementById("carro").value.trim();
        const modelo = document.getElementById("modelo").value.trim();
        const placa = document.getElementById("placa").value.trim();

        if (!carro || !modelo || !placa) {
            alert("Preencha todas as informações do veículo.");
            return;
        }

        if (fotoPerfil) {
            fd.append("foto_perfil", fotoPerfil);
        }
        fd.append("carro", carro);
        fd.append("modelo", modelo);
        fd.append("placa", placa);
    }

    const retorno = await fetch("../php/usuario_cadastro.php", {
        method: "POST",
        body: fd
    });
    const resposta = await retorno.json();

    if (resposta.status === "ok") {
        alert("Cadastro realizado com sucesso! Faça login.");
        window.location.href = "../login/";
    } else {
        alert("Erro: " + resposta.mensagem);
    }
});