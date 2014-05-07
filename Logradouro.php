<?php
    namespace CopaControleVeiculos;

    require_once('Connection.php');
    require_once('Limites.php');

    class Logradouro {

        private $tipoEndereco;
        private $codLogradouro;
        private $nomeLogradouro;
        private $numero;
        private $complemento;
        private $bairro;
        private $cep;

        function __construct() {
        }

        public function getTipoEndereco() {
            return $this->tipoEndereco;
        }

        public function setTipoEndereco($tipoEndereco) {
            $this->tipoEndereco = $tipoEndereco;
        }

        public function getCodLogradouro() {
            return $this->codLogradouro;
        }

        public function setCodLogradouro($codLogradouro) {
            $this->codLogradouro = $codLogradouro;
        }
        
        public function getNomeLogradouro() {
            return $this->nomeLogradouro;
        }

        public function setNomeLogradouro($nomeLogradouro) {
            $this->nomeLogradouro = $nomeLogradouro;
        }

        public function getNumero() {
            return $this->numero;
        }

        public function setNumero($numero) {
            $this->numero = $numero;
        }
        
        public function getComplemento() {
            return $this->complemento;
        }

        public function setComplemento($complemento) {
            $this->complemento = $complemento;
        }

        public function getBairro() {
            return $this->bairro;
        }

        public function setBairro($bairro) {
            $this->bairro = $bairro;
        }

        public function getCep() {
            return $this->cep;
        }

        public function setCep($cep) {
            $this->cep = $cep;
        }


    }      
        
?>
