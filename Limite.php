<?php
    namespace CopaControleVeiculos;

    class Limite {
        private $codLogradouro;
        private $nomeLogradouro;
        private $lado;
        private $numeroInicial;
        private $numeroFinal;

        function __construct($codLogradouro, $nomeLogradouro, $lado, $numeroInicial, $numeroFinal) {
            $this->codLogradouro = $codLogradouro;
            $this->nomeLogradouro = $nomeLogradouro;
            $this->lado = $lado;
            $this->numeroInicial = $numeroInicial;
            $this->numeroFinal = $numeroFinal;
        }

        public function getCodLogradouro() {
            return $this->codLogradouro;
        }

        public function getNomeLogradouro() {
            return $this->nomeLogradouro;
        }

        public function getLado() {
            return $this->lado;
        }

        public function getNumeroInicial() {
            return $this->numeroInicial;
        }
        
        public function getNumeroFinal() {
            return $this->numeroFinal;
        }
    }
?>
