const display = document.querySelector('.display');
const alertElement = document.getElementById('Alert');
let confirmarVotacao = false; // Variável para rastrear a confirmação
let Matricula ="";
function addNumero(num) {
    display.value += num;
}

function apagar() {
    if (confirmarVotacao) {
      location.reload();
      return;
    }

    if (display.value) {  
      display.value = display.value.slice(0, -1);
    }
}

function enviar() {
    if (confirmarVotacao) {
        // Redireciona para outra página se já houve a confirmação
        window.location.href = `Votacao/Votacao.php?matricula=${Matricula}`;
        return;
    }

    if (display.value) {
        verificarMatricula(display.value);
    }
}

function verificarMatricula(matricula) {
    // Cria um objeto FormData para enviar os dados
    const formData = new FormData();
    formData.append('matricula', matricula);
    Matricula = matricula;
    // Faz a requisição para o PHP
    fetch('verificar.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text()) // Obtém a resposta como texto
        .then(resultado => {
            if (resultado === 'nao_encontrado') { // Verifica respostas simples primeiro
                alertElement.textContent = 'Matrícula não encontrada.';
                confirmarVotacao = false;
            } else if (resultado === 'ja_votou') {
                alertElement.textContent = 'Este discente já votou.';
                confirmarVotacao = false;
            } else if (resultado === 'erro_banco') {
                alertElement.textContent = 'Erro ao acessar o banco de dados. Contate o suporte.';
                confirmarVotacao = false;
            } else if (resultado === 'erro') {
                alertElement.textContent = 'Ocorreu um erro. Contate o suporte.';
                confirmarVotacao = false;
            }
            else {
                try {
                    const data = JSON.parse(resultado);
                    if (data.status === 'nao_votou') {
                        alertElement.textContent = `Voce confirmar ser ${data.nome}, Pressione Confirmar novamente para continuar Ou Apagar para Cancelar`;
                        confirmarVotacao = true;
                    } 
                } catch (e) {
                    console.error('Erro ao processar resposta do servidor (JSON inválido):', resultado, e);
                    alertElement.textContent = 'Erro ao verificar matrícula (Resposta inválida do servidor).';
                    confirmarVotacao = false;
                }
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alertElement.textContent = 'Erro ao verificar matrícula.';
            confirmarVotacao = false;
        });
}

// Adiciona suporte a digitação pelo teclado
document.addEventListener('keydown', (e) => {
    if (e.key >= '0' && e.key <= '9') {
        addNumero(e.key);
    } else if (e.key === 'Enter') {
        enviar();
    } else if (e.key === 'Backspace') {
        apagar();
    }
});
