<?php
    namespace CopaControleVeiculos;

    class Veiculo {
        
        private $placa;
        private $codRenavam;
        private $marca;
        private $modelo;
        private $ano;
        private $cor;
        private $garagem;
        private $cartaoEntregue;
        private $dataHoraCartao;

        function __construct() {
        }

        public function getPlaca() {
            return $this->placa;
        }

        public function setPlaca($placa) {
            $this->placa = $placa;
        }

        public function getCodRenavam() {
            return $this->codRenavam;
        }

        public function setCodRenavam($codRenavam) {
            $this->codRenavam = $codRenavam;
        }

        public function getMarca() {
            return $this->marca;
        }

        public function setMarca($marca) {
            $this->marca = $marca;
        }

        public function getModelo() {
            return $this->modelo;
        }

        public function setModelo($modelo) {
            $this->modelo = $modelo;
        }

        public function getAno() {
            return $this->ano;
        }

        public function setAno($ano) {
            $this->ano = $ano;
        }

        public function getCor() {
            return $this->cor;
        }

        public function setCor($cor) {
            $this->cor = $cor;
        }

        public function getGaragem() {
            return $this->garagem;
        }
        
        public function setGaragem($garagem) {
            $this->garagem = $garagem;
        }

        public function getCartaoEntregue() {
            return $this->cartaoEntregue;
        }

        public function setCartaoEntregue($cartaoEntregue) {
            $this->cartaoEntregue = $cartaoEntregue;
        }

        public function getDataHoraCartao() {
            return $this->dataHoraCartao;
        }

        public function setDataHoraCartao($dataHoraCartao) {
            $this->dataHoraCartao = $dataHoraCartao;
        }
    }
?>
