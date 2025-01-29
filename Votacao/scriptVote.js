const mensagemDiv = document.getElementById('mensagem');
    function votarChapa(nomeChapa, matricula) {
        let confirmacao = confirm(`Deseja votar na chapa ${nomeChapa}?`);
        if (confirmacao) {
            fetch('votar.php?chapa=' + encodeURIComponent(nomeChapa) + '&matricula=' + encodeURIComponent(matricula))
                .then(response => response.text())
                .then(data => {
                    mostrarMensagem();
                    setTimeout(() => {
                        window.location.href = '../';  // ou 'http://localhost/urna/index.html'
                    }, 4000);
                })
                .catch(error => {
                    console.error('Erro ao votar:', error);
                    alert('Erro ao votar. Tente novamente!');
                });
                function mostrarMensagem() {
                    mensagemDiv.style.visibility = "visible";
                    setTimeout(() => {
                        mensagemDiv.style.display = "hidden";
                    }, 3000);
                }
        }
    }