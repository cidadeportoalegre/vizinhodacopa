<?php
    namespace CopaControleVeiculos;

    require_once('Logradouro.php');

    class Proprietario {
    
        private $tipoPessoa;
        private $nroCpfCnpj;
        private $nomeRzSocial;
        private $telefone;
        private $veiculos;
        private $proprietarioConfirmado;
        private $logradouro;
       
        public function __construct() {
            $this->veiculos = array();
            $this->proprietarioConfirmado = FALSE;
            $this->logradouro = new \CopaControleVeiculos\Logradouro();
        }
 
        public function getTipoPessoa() {
            return $this->tipoPessoa;
        }        

        public function setTipoPessoa($tipoPessoa) {
            $this->tipoPessoa = $tipoPessoa;
        }

        public function getNroCpfCnpj() {
            return $this->nroCpfCnpj;
        }

        public function setNroCpfCnpj($nroCpfCnpj) {
            $this->nroCpfCnpj = $nroCpfCnpj;
        }

        public function getNomeRzSocial() {
            return $this->nomeRzSocial;
        }

        public function setNomeRzSocial($nomeRzSocial) {
            $this->nomeRzSocial = $nomeRzSocial;
        }

        public function getTelefone() {
            return $this->telefone;
        }

        public function setTelefone($telefone) {
            $this->telefone = $telefone;
        }

        public function getVeiculos() {
            return $this->veiculos;
        }

        public function setVeiculos($veiculos) {
            $this->veiculos = $veiculos;
        }

        public function isProprietarioConfirmado() {
            return $this->proprietarioConfirmado;
        }

        public function setProprietarioConfirmado($proprietarioConfirmado) {
            $this->proprietarioConfirmado = $proprietarioConfirmado;
        }

        public function getLogradouro() {
            return $this->logradouro;
        }

        public function setLogradouro($logradouro) {
            $this->logradouro = $logradouro;
        }

        public function addVeiculo($veiculo) {
            if (!array_key_exists($veiculo->getCodRenavam(), $this->veiculos)) {
                if ($this->getVeiculo($veiculo->getPlaca()) != NULL) {
                    return 0;
                }
                else {
                    if (count($this->getVeiculos()) <= 50) {
                        $this->veiculos[$veiculo->getCodRenavam()] = $veiculo;
                        return 1;
                    }
                    else {
                        return 0;
                    }
                }
            }
            else {
                return 0;
            }
        }

        public function getVeiculo($placa) {
            if ($placa != '') {
                foreach ($this->getVeiculos() as $value) {
                    if (!strcasecmp($value->getPlaca(), $placa)) {
                        return $value;
                    }
                }
            }

            return NULL;
        }

        public function veiculosComoDiv() {
            $veiculos = $this->getVeiculos();
            $links = '';
            if (isset($veiculos) && !empty($veiculos)) {
                foreach ($veiculos as $value) {
                    $link = sprintf('<div id="%s" title="%s">', $value->getPlaca(), $value->getPlaca());
                    $link .= '<ul>';
                    $link .= sprintf('<li>Placa: %s</li>', $value->getPlaca());
                    $link .= '<li>RENAVAM: ' . $value->getCodRenavam() . '</li>';
                    $link .= '<li>Marca: ' . $value->getMarca() . '</li>';
                    $link .= '<li>Modelo: ' . $value->getModelo() . '</li>';
                    $link .= '<li>Ano: ' . $value->getAno() . '</li>';
                    $link .= '<li>Cor: ' . $value->getCor() . '</li>';
//                    $link .= '<li>Garagem: ' . ($value->getGaragem() == 'S' ? 'Sim' : 'Não') . '</li>';
                    if ($value->getCartaoEntregue() == 'S') {
                        $link .= '<li>';
                        $link .= htmlentities('Cartão entregue em ' . $value->getDataHoraCartao(), 0 | 2, 'ISO-8859-1', FALSE);
                        $link .= '</li>';
                    }
                    else {
                        $link .= '<li>';
                        $link .= htmlentities('Cartão entregue: não', 0 | 2, 'ISO-8859-1', FALSE);
                        $link .= '</li>';
                    }
                    $link .= '</ul>';
                    $link .= '</div>';
                    $links .= $link;
                }
            }
            return $links;
        }

        public function logradourosVeiculosComoDiv() {
            $veiculos = $this->getVeiculos();
            $links = '';

            if (isset($veiculos) && !empty($veiculos)) {
                foreach ($veiculos as $value) {
                    $link = sprintf('<div id="%s">', $value->getPlaca());
                    $link .= '<ul>';
                    $link .= sprintf('<li>Placa: %s</li>', $value->getPlaca());
                    $link .= '<li>RENAVAM: ' . $value->getCodRenavam() . '</li>';
                    $link .= '<li>Marca: ' . $value->getMarca() . '</li>';
                    $link .= '<li>Modelo: ' . $value->getModelo() . '</li>';
                    $link .= '<li>Ano: ' . $value->getAno() . '</li>';
                    $link .= '<li>Cor: ' . $value->getCor() . '</li>';
//                    $link .= '<li>Garagem: ' . ($value->getGaragem() == 'S' ? 'Sim' : 'Não') . '</li>';
                    if ($value->getCartaoEntregue() == 'S') {
                        $link .= '<li>Cartão entregue em ' . $value->getDataHoraCartao() . '</li>';
                    }
                    else {
                        $link .= '<li>Cartão entregue: não</li>';
                    }
                    $logradouro = $this->getLogradouro();
                    if (!empty($logradouro)) {
                        $link .= '<li>Endereço: '  .  $logradouro->getNomeLogradouro() . '</li>';
                        $link .= '<li>Número: '. $logradouro->getNumero() . '</li>';
                        $complemento = $logradouro->getComplemento();
                        if (!empty($complemento)) {
                            $link .= '<li>Complemento: ' . $complemento . '</li>';
                        }
                        $link .= '<li>Bairro: ' . $logradouro->getBairro() . '</li>';
                        $link .= '<li>CEP: ' . $logradouro->getCep() . '</li>';
                    }
                    $link .= '</ul>';
                    $link .= '</div>';
                    $links .= $link;
                }
            }
            return $links;
                    
        }

        public function linksVeiculosComoUl() {
            $veiculos = $this->getVeiculos();
            $links = '<div id="veiculosLinks">';
            $links .= '<ul>';

            if (isset($veiculos) && !empty($veiculos)) {
                foreach ($veiculos as $value) {
                    $links .= sprintf('<li><a href="" id="link%s">Placa: %s</a></li>', $value->getPlaca(), $value->getPlaca());
//                    $links .= sprintf('<button id="%s">%s</button>', 'link' . $value->getPlaca(), $value->getPlaca());  
                }
            }

            $links .= '</ul>';
            $links .= '</div>';
            return $links;
        }

        public function scriptDialogs() {
            $veiculos = $this->getVeiculos();
            $script = '<script>';
            
            if (isset($veiculos) && !empty($veiculos)) {
                foreach ($veiculos as $value) {
                    $script .= '$(function() {';
                    $script .= sprintf('$( "#%s" ).dialog({', $value->getPlaca());
                    $script .= 'autoOpen: false';
                    $script .= '});';
                    $script .= sprintf('$( "#%s" ).click(function() {', 'link' . $value->getPlaca());
                    $script .= sprintf('$( "#%s" ).dialog( "open" );', $value->getPlaca());
                    $script .= 'return false;';
                    
                    $script .= '});';

                    $script .= '});';
                }
            }
            $script .= '</script>';
            return $script;
        }
             
    }
?>
