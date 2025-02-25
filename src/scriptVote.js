const mensagemDiv = document.getElementById('mensagem');
const audio = new Audio('../public/audio/AudioUrna.m4a');
    function playAudio() {
        audio.play(); // Inicia a reprodução
    }
    function votarChapa(nomeChapa, matricula) {
        let confirmacao = confirm(`Deseja votar na chapa ${nomeChapa}?`);
        if (confirmacao) {
            fetch('votar.php?chapa=' + encodeURIComponent(nomeChapa) + '&matricula=' + encodeURIComponent(matricula))
                .then(response => response.text())
                .then(data => {
                    mostrarMensagem();
                    playAudio();
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 4000);
                })
                .catch(error => {
                    console.error('Erro ao votar:', error);
                    alert('Erro ao votar. Tente novamente!');
                });
                function mostrarMensagem() {
                    mensagemDiv.classList.remove('hidden'); // Faz aparecer
                }                
        }
    }