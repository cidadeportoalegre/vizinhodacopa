<?php
    namespace CopaControleVeiculos;

    require_once('Util.php');
    require_once('Logradouro.php');
    require_once('Connection.php');
    require_once('Limites.php');
    require_once('Proprietario.php');
    require_once('Veiculo.php');

    class Cadastro {
        
        public static $INSERT_CADASTRO = 'INSERT INTO Veiculo (CodRenavam, Placa, Marca, Modelo, Ano, Cor, DataHoraCadastro, TipoPessoa_CPF_CNPJ, NroCpfCnpj, Nome_RzSocial, TipoEndereco_R_C, CodLogradouro, NomeLogradouro, Numero, Complemento, Bairro, Cep, Telefone, CartaoEntregue_S_N, DataHoraCartao, PlacaAnterior, Cpf_Aprovador) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)';

        public static $SELECT_CADASTRO = 'SELECT * FROM Veiculo WHERE NroCpfCnpj = %s';

        public static $SELECT_CODRENAVAM = 'SELECT NroCpfCnpj, CodRenavam FROM Veiculo WHERE CodRenavam = %s';

        public static $SELECT_PLACA = 'SELECT NroCpfCnpj, Placa FROM Veiculo WHERE Placa = %s';

        public static $SELECT_ULTIMO_PROPRIETARIO = 'SELECT * FROM Veiculo WHERE DataHoraCadastro IN (SELECT MAX(DataHoraCadastro) FROM Veiculo) AND NroCpfCnpj = %s';

        public static $SELECT_CODRENAVAM_WHERE_CPF = 'SELECT CodRenavam FROM Veiculo WHERE CodRenavam = %s AND NroCpfCnpj = %s';

        public static $SUBMIT_PROPRIETARIO = 'salvar';

        public static $AVANCA_PROPRIETARIO = 'avancar';

        public static $SESSION_INDEX = 'cadastro';
        
        public static $CAMPOS = array(
                'tipoPessoa' => 'proprietarioTipoPessoa',
                'nroCpfCnpj' => 'proprietarioCpfCnpj',
                'nomeRzSocial' => 'proprietarioNome',
                'tipoEndereco' => 'logradouroTipoEndereco',
                'telefone' => 'proprietarioTelefone',
                'codLogradouro' => 'logradouroCod',
                'nomeLogradouro' => 'logradouroNome',
                'numero' => 'logradouroNumero',
                'complemento' => 'logradouroComplemento',
                'bairro' => 'logradouroBairro',
                'cep' => 'logradouroCep',
                'placa' => 'veiculoPlaca',
                'codRenavam' => 'veiculoCodRenavam',
                'garagem' => 'veiculoGaragem',
                'marca' => 'veiculoMarca',
                'modelo' => 'veiculoModelo',
                'ano' => 'veiculoAno',
                'cor' => 'veiculoCor'
        ); 

        public static $ERROS = array(
                'proprietarioTipoPessoa' => 'Selecione cpf ou cnpj.',
                'proprietarioCpfCnpj' => 'CPF/CNPJ inválido.',
                'proprietarioNome' => 'Informe o nome ou a razão social.',
                'proprietarioTelefone' => 'Telefone inválido.',
                'logradouroTipoEndereco' => 'Selecione o tipo de endereço.',
                'logradouroCod' => 'Código de logradouro inválido.',
                'logradouroNome' => 'Selecione o nome do logradouro.',
                'logradouroNumero' => 'O cadastramento do veículo é desnecessário pois não há interrupção na via referente ao número informado.',
                'logradouroBairro' => 'Informe o bairro.',
                'logradouroCep' => 'Informe o cep.',
                'veiculoPlaca' => 'Informe a placa.',
                'veiculoCodRenavam' => 'Informe o RENAVAM.',
                'veiculoMarca' => 'Informe a marca.',
                'veiculoModelo' => 'Informe o modelo.',
                'veiculoAno' => 'Informe o ano.',
                'veiculoCor' => 'Informe a cor.',
                'veiculoGaragem' => 'Informe Sim/Não.'
        );     

        private $erros;
        private $errosGenericos;
        private $alertasGenericos;
        private $mensagensGenericas;

        function __construct() {
            $this->erros = array();
            $this->errosGenericos = array();
            $this->alertasGenericos = array();
            $this->mensagensGenericas = array();
        } 

        public static function getCampo($id) {
            return self::$CAMPOS[$id];
        }

        public static function getMsgErro($id) {
            return self::$ERROS[$id];
        }
        
        public function setErro($id) {
            $this->erros[$id] = self::getMsgErro($id);
        }

        public function setErroEspecifico($id, $mensagem) {
            $this->erros[$id] = $mensagem;
        }

        public function getErro($id) {
            if (array_key_exists($id, $this->erros)) {
                return $this->erros[$id];
            }
            return '';
        }

        public function getErros() {
            return $this->erros;
        }

        public function getErrosGenericos() {
            return $this->errosGenericos;
        }

        public function getAlertasGenericos() {
            return $this->alertasGenericos;
        }

        public function getMensagensGenericas() {
            return $this->mensagensGenericas;
        }

        public function validaCadastro($post) {
            $this->erros = array();

            if (!$this->validaProprietarioTipoPessoa($post)) {
                $this->adicionaErroGenerico(htmlentities('Selecione CPF ou CNPJ.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaNroCpfCnpj($post)) {
                $this->adicionaErroGenerico(htmlentities('CPF/CNPJ inválido.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaNomeRzSocial($post)) {
                $this->adicionaErroGenerico(htmlentities('Informe o nome ou a Razão Social.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaTelefone($post)) {
                $this->adicionaErroGenerico(htmlentities('Telefone inválido.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaTipoEndereco($post)) {
                $this->adicionaErroGenerico(htmlentities('Selecione o tipo de endereço.', 2 | 0, 'ISO-8859-1', FALSE));
            }
            
            if (!$this->validaCodLogradouro($post)) {
                $this->adicionaErroGenerico(htmlentities('Código de logradouro inválido.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaNomeLogradouro($post)) {
                $this->adicionaErroGenerico(htmlentities('Selecione o nome do logradouro.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaNumero($post)) {
                $this->adicionaErroGenerico(htmlentities('Número não permitido.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaComplemento($post)) {
                $this->adicionaErroGenerico(htmlentities('Complemento inválido.', 2 | 0, 'ISO-8859-1', FALSE));
            }

            if (!$this->validaBairro($post)) {
                if (empty($this->erros[self::getCampo('bairro')])) {
                    $this->adicionaErroGenerico(htmlentities('Informe o bairro.', 2 | 0, 'ISO-8859-1', FALSE));
                }
            }

            if (!$this->validaCep($post)) {
                if (empty($this->erros[self::getCampo('cep')])) {
                    $this->adicionaErroGenerico(htmlentities('Informe o CEP.', 2 | 0, 'ISO-8859-1', FALSE));
                }
            }

//            if (!$this->validaGaragem($post)) {
//                $this->adicionaErroGenerico(htmlentities('Informe se há ou não garagem para o veículo.', 2 | 0, 'ISO-8859-1', FALSE));
//            }

            if (!$this->validaPlaca($post)) {
                if (empty($this->erros[self::getCampo('placa')])) {
                    $this->adicionaErroGenerico('Informe a placa.');
                }
            }

            if (!$this->validaCodRenavam($post)) {
                if (empty($this->erros[self::getCampo('codRenavam')])) {
                    $this->adicionaErroGenerico('Informe o RENAVAM.');
                }
            }

            if (!$this->validaMarca($post)) {
                $this->adicionaErroGenerico('Informe a marca.');
            }

            if (!$this->validaModelo($post)) {
                $this->adicionaErroGenerico('Informe o modelo.');
            }

            if (!$this->validaAno($post)) {
                if (empty($this->erros[self::getCampo('ano')])) {
                    $this->adicionaErroGenerico('Informe o ano.');
                }
            }

            if (!$this->validaCor($post)) {
                $this->adicionaErroGenerico('Informe a cor.');
            }

            if (!empty($this->errosGenericos)) {
                return 0;
            }
            return 1;
        }
        
        public function defineProprietario($post) {
            $proprietario = new \CopaControleVeiculos\Proprietario();
            
            $proprietario->setTipoPessoa($post[self::getCampo('tipoPessoa')]);
            $proprietario->setNroCpfCnpj($post[self::getCampo('nroCpfCnpj')]);
            $proprietario->setNomeRzSocial($post[self::getCampo('nomeRzSocial')]);
            $proprietario->setTelefone($post[self::getCampo('telefone')]);
            $proprietario->getLogradouro()->setTipoEndereco($post[self::getCampo('tipoEndereco')]);
            $proprietario->getLogradouro()->setCodLogradouro($post[self::getCampo('codLogradouro')]);
            $proprietario->getLogradouro()->setNomeLogradouro(\CopaControleVeiculos\Limites::getNomeLogradouro($post[self::getCampo('codLogradouro')]));
            $proprietario->getLogradouro()->setNumero($post[self::getCampo('numero')]);
            $proprietario->getLogradouro()->setComplemento($post[self::getCampo('complemento')]);
            $proprietario->getLogradouro()->setBairro($post[self::getCampo('bairro')]);
            $proprietario->getLogradouro()->setCep($post[self::getCampo('cep')]);
            
            $veiculo = new \CopaControleVeiculos\Veiculo();
            $veiculo->setPlaca($post[self::getCampo('placa')]);
            $veiculo->setCodRenavam($post[self::getCampo('codRenavam')]);
            $veiculo->setMarca($post[self::getCampo('marca')]);
            $veiculo->setModelo($post[self::getCampo('modelo')]);
            $veiculo->setAno($post[self::getCampo('ano')]);
            $veiculo->setCor($post[self::getCampo('cor')]);
//            $veiculo->setGaragem($post[self::getCampo('garagem')]);
            
            $proprietario->addVeiculo($veiculo);
            return $proprietario;
        }

        public function validaProprietarioTipoPessoa($post) {
            if (!empty($post[self::getCampo('tipoPessoa')])) {
                $tipoPessoa = $post[self::getCampo('tipoPessoa')];
                
                if ($tipoPessoa === 'cpf' || $tipoPessoa === 'cnpj') {
                    return 1;
                }   
            }
            return 0;
        }

        public function validaNroCpfCnpj($post) {
            if (!empty($post[self::getCampo('tipoPessoa')]) && self::validaProprietarioTipoPessoa($post) && !empty($post[self::getCampo('nroCpfCnpj')])) {
                $tipoPessoa = $post[self::getCampo('tipoPessoa')];
                $documento = $post[self::getCampo('nroCpfCnpj')];
                
                if ($tipoPessoa === 'cpf') {
                    if (\CopaControleVeiculos\Util::cpfRegex($documento)) {
                        $documento = \CopaControleVeiculos\Util::insertPad($documento, 11);
                        if (\CopaControleVeiculos\Util::validaCpf($documento)) {
                            return 1;
                        }
                    }
                }
                if ($tipoPessoa === 'cnpj') {
                    if (\CopaControleVeiculos\Util::cnpjRegex($documento)) {
                        $documento = \CopaControleVeiculos\Util::insertPad($documento, 14);
                        if (\CopaControleVeiculos\Util::validaCnpj($documento)) {
                            return 1;
                        }
                    }
                }
            }
            return 0;
        }

        public function validaNomeRzSocial($post) {
            if (!empty($post[self::getCampo('nomeRzSocial')])) {
                $nome = $post[self::getCampo('nomeRzSocial')];
                
                if (\CopaControleVeiculos\Util::nomeRegex($nome)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaTipoEndereco($post) {
            if (!empty($post[self::getCampo('tipoEndereco')])) {
                $tipoEndereco = $post[self::getCampo('tipoEndereco')];
        
                if ($tipoEndereco === 'R' || $tipoEndereco === 'C') {
                    return 1;
                }
            }
            return 0;
        }

        public function validaCodLogradouro($post) {
            if (!empty($post[self::getCampo('codLogradouro')])) {
                $codLogradouro = $post[self::getCampo('codLogradouro')];
                
                if (\CopaControleVeiculos\Limites::verificaCodLogradouro($codLogradouro)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaNomeLogradouro($post) {
            if (!empty($post[self::getCampo('codLogradouro')])) {
                $codLogradouro = $post[self::getCampo('codLogradouro')];

                $nomeLogradouro = \CopaControleVeiculos\Limites::getNomeLogradouro($codLogradouro);
                
                if (!empty($nomeLogradouro)) {
                    if (\CopaControleVeiculos\Limites::verificaNomeLogradouro($nomeLogradouro)) {
                        return 1;
                    }
                }
            }
            return 0;
        }

        public function validaNumero($post) {
            if (!empty($post[self::getCampo('numero')])) {
                $numero = $post[self::getCampo('numero')];
                
                if ($this->validaCodLogradouro($post)) {
                    $codLogradouro = $post[self::getCampo('codLogradouro')];
                    if (\CopaControleVeiculos\Limites::verificaLimite($codLogradouro, $numero)) {
                        return 1;
                    }
                }
            }        
            return 0;
        }

        public function validaComplemento($post) {
            if (isset($post[self::getCampo('complemento')])) {
                $complemento = $post[self::getCampo('complemento')];
    
                if ($complemento == '' || \CopaControleVeiculos\Util::complementoRegex($complemento)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaBairro($post) {
            if (!empty($post[self::getCampo('bairro')])) {
                $bairro = $post[self::getCampo('bairro')];

                if (\CopaControleVeiculos\Util::bairroRegex($bairro)) {
                    return 1;
                }
                $this->setErro(self::getCampo('bairro'));
                $this->adicionaErroGenerico(htmlentities('bairro inválido', 2 | 0, 'ISO-8859-1', FALSE));
            }
            return 0;
        }

        public function validaCep($post) {
            if (!empty($post[self::getCampo('cep')])) {
                $cep = $post[self::getCampo('cep')];

                if (\CopaControleVeiculos\Util::cepRegex($cep)) {
                    return 1;
                }
                $this->setErro(self::getCampo('cep'));
                $this->adicionaErroGenerico(htmlentities('cep inválido', 2 | 0, 'ISO-8859-1', FALSE));
            }
            return 0;
        }

        public function validaTelefone($post) {
            if (isset($post[self::getCampo('telefone')])) {
                $telefone = $post[self::getCampo('telefone')];
                
                if ($telefone == '' || \CopaControleVeiculos\Util::telefoneRegex($telefone)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaPlaca($post) {
            if (!empty($post[self::getCampo('placa')])) {
                $placa = $post[self::getCampo('placa')];

                if (\CopaControleVeiculos\Util::placaRegex($placa)) {
                    return 1;
                }
                $this->setErro(self::getCampo('placa'));
                $this->adicionaErroGenerico(htmlentities('placa inválida', 2 | 0, 'ISO-8859-1', FALSE));
            }
            return 0;
        }

        public function validaCodRenavam($post) {
            if (!empty($post[self::getCampo('codRenavam')])) {
                $codRenavam = $post[self::getCampo('codRenavam')];

                if (\CopaControleVeiculos\Util::renavamRegex($codRenavam)) {
                    $codRenavam = \CopaControleVeiculos\Util::insertPad($codRenavam, 11);
                    if (\CopaControleVeiculos\Util::validaRenavam($codRenavam)) {
                        return 1;
                    }
                }
                $this->setErro(self::getCampo('codRenavam'));
                $this->adicionaErroGenerico(htmlentities('RENAVAM inválido', 2 | 0, 'ISO-8859-1', FALSE));
            }
            return 0;
        }

        public function validaMarca($post) {
            if (!empty($post[self::getCampo('marca')])) {
                $marca = $post[self::getCampo('marca')];
    
                if (\CopaControleVeiculos\Util::marcaRegex($marca)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaModelo($post) {
            if (!empty($post[self::getCampo('modelo')])) {
                $modelo = $post[self::getCampo('modelo')];

                if (\CopaControleVeiculos\Util::marcaRegex($modelo)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaAno($post) {
            if (!empty($post[self::getCampo('ano')])) {
                $ano = $post[self::getCampo('ano')];

                if (\CopaControleVeiculos\Util::anoRegex($ano)) {
                    if ($ano >= 1900 && $ano <= 2015) {
                        return 1;
                    }
                    $this->setErro(self::getCampo('ano'));
                    $this->adicionaErroGenerico(htmlentities('ano inválido', 2 | 0, 'ISO-8859-1', FALSE)); 
                }
            }
            return 0;
        }

        public function validaCor($post) {
            if (!empty($post[self::getCampo('cor')])) {
                $cor = $post[self::getCampo('cor')];

                if (\CopaControleVeiculos\Util::corRegex($cor)) {
                    return 1;
                }
            }
            return 0;
        }

        public function validaGaragem($post) {
            if (!empty($post[self::getCampo('garagem')])) {
                $garagem = $post[self::getCampo('garagem')];

                if ($garagem === 'S' || $garagem === 'N') {
                    return 1;
                }
            }
            return 0;
        }

        public function getInputValue($inputId, $proprietario, $post) {
            if ($proprietario->isProprietarioConfirmado()) {
                if ($inputId == self::getCampo('nomeRzSocial')) {
                    return $proprietario->getNomeRzSocial();
                }

                if ($inputId == self::getCampo('nroCpfCnpj')) {
                    return $proprietario->getNroCpfCnpj();
                }

                if ($inputId == self::getCampo('telefone')) {
                    return $proprietario->getTelefone();
                }
                
                $logradouro = NULL;
                
                if (!empty($proprietario)) { 
                    $logradouro = $proprietario->getLogradouro();
                }
    
                if (!empty($logradouro)) {
                    if ($inputId == self::getCampo('numero')) {
                        return $proprietario->getLogradouro()->getNumero();
                    }

                    if ($inputId == self::getCampo('complemento')) {
                        return $proprietario->getLogradouro()->getComplemento();
                    }

                    if ($inputId == self::getCampo('bairro')) {
                        return $proprietario->getLogradouro()->getBairro();
                    }

                    if ($inputId == self::getCampo('cep')) {
                        return $proprietario->getLogradouro()->getCep();
                    }
                }

                if (isset($post) && (isset($post['salvar']) || isset($post['finalizar']))) {
                    if ($inputId == self::getCampo('codRenavam')) {
                        return $post[$inputId];
                    }
                    
                    if ($inputId == self::getCampo('placa')) {
                        return $post[$inputId];
                    }

                    if ($inputId == self::getCampo('marca')) {
                        return $post[$inputId];
                    }

                    if ($inputId == self::getCampo('modelo')) {
                        return $post[$inputId];
                    }

                    if ($inputId == self::getCampo('ano')) {
                        return $post[$inputId];
                    }

                    if ($inputId == self::getCampo('cor')) {
                        return $post[$inputId];
                    }
                }
                
            }
            if (isset($post[$inputId])) {
                return $post[$inputId];
            }
            return '';
        }

        public function getTipoPessoaChecked($tipoPessoa, $proprietario, $post) {
            if (!empty($proprietario)) {
                if ($proprietario->isProprietarioConfirmado()) {
                    if ($proprietario->getTipoPessoa() == $tipoPessoa) {
                        return 'checked';
                    }
                }
            }
            if (isset($post[self::$SUBMIT_PROPRIETARIO]) || isset($post[self::$AVANCA_PROPRIETARIO])) {
                if (isset($post[self::getCampo('tipoPessoa')]) && $post[self::getCampo('tipoPessoa')] == $tipoPessoa) {
                    return 'checked';
                }
            }
            return '';
        }
       
        public function getTipoEnderecoChecked($tipoEndereco, $proprietario, $post) {
            if (!empty($proprietario)) {
                if ($proprietario->isProprietarioConfirmado()) {
                
                    $logradouro = $proprietario->getLogradouro();
                
                    if (!empty($logradouro) && $proprietario->getLogradouro()->getTipoEndereco() == $tipoEndereco) {
                        return 'checked';
                    }
                }
            } 
            if (isset($post[self::getCampo('tipoEndereco')]) && $post[self::getCampo('tipoEndereco')] == $tipoEndereco) {
                return 'checked';
            }
            return '';
        }

        public function getGaragemChecked($garagem, $post) {
           if (isset($post[self::getCampo('garagem')]) && $post[self::getCampo('garagem')] == $garagem) {
                return 'checked';
            }
            return '';
        } 
        
        public function validaAdicionaVeiculo($post) {
            $this->erros = array();

//            if (!$this->validaGaragem($post)) {
//                $this->adicionaErroGenerico(htmlentities('Informe se há ou não garagem para o veículo.', 2 | 0, 'ISO-8859-1', FALSE));
//            }

            if (!$this->validaPlaca($post)) {
                if (empty($this->erros[self::getCampo('placa')])) {
                    $this->adicionaErroGenerico('Informe a placa.');
                }
            }

            if (!$this->validaCodRenavam($post)) {
                if (empty($this->erros[self::getCampo('codRenavam')])) {
                    $this->adicionaErroGenerico('Informe o RENAVAM.');
                }
            }

            if (!$this->validaMarca($post)) {
                $this->adicionaErroGenerico('Informe a Marca');
            }

            if (!$this->validaModelo($post)) {
                $this->adicionaErroGenerico('Informe o modelo.');
            }

            if (!$this->validaAno($post)) {
                if (empty($this->erros[self::getCampo('ano')])) {
                    $this->adicionaErroGenerico('Informe o ano.');
                }
            }

            if (!$this->validaCor($post)) {
                $this->adicionaErroGenerico('Informe a cor.');
            }

            if (!empty($this->errosGenericos)) {
                return 0;
            }
            return 1;
        }

        public function adicionaVeiculo($post, $proprietario) {
            if (isset($proprietario) && $proprietario->isProprietarioConfirmado()) {
                $veiculo = new \CopaControleVeiculos\Veiculo();

                $veiculo->setPlaca($post[self::getCampo('placa')]);
                $veiculo->setCodRenavam($post[self::getCampo('codRenavam')]);
                $veiculo->setMarca($post[self::getCampo('marca')]);
                $veiculo->setModelo($post[self::getCampo('modelo')]);
                $veiculo->setAno($post[self::getCampo('ano')]);
                $veiculo->setCor($post[self::getCampo('cor')]);
//                $veiculo->setGaragem($post[self::getCampo('garagem')]);

                if (!$proprietario->addVeiculo($veiculo)) {
                    $this->adicionaErroGenerico(htmlentities('Veículo já adicionado.', 2 | 0, 'ISO-8859-1', FALSE));
                }

                return $proprietario;
            }
        }

        public function veiculoAdicionado($proprietario, $placa) {
            $veiculo = $proprietario->getVeiculo($placa);
            if (empty($veiculo)) {
                return FALSE;
            }
            else {
                return TRUE;
            }
        }

        public function isInputDisabled($proprietario) {
            if ($proprietario->isProprietarioConfirmado()) {
                return 'disabled';
            }
            return '';
        }

        public function finalizaCadastro($proprietario) {
            \date_default_timezone_set('America/Sao_Paulo');
            $erroNaInsercao = FALSE;

            $connection = new \CopaControleVeiculos\Connection();
            
            $veiculos = $proprietario->getVeiculos();
            
            foreach ($veiculos as $value) {
                $existeCodRenavam = self::$SELECT_CODRENAVAM;
                $existeCodRenavam = sprintf($existeCodRenavam, $connection->realEscapeString($value->getCodRenavam()));
                $resultCodRenavam = $connection->executeQuery($existeCodRenavam);
                $errorExisteCodRenavam = $connection->getErrorNo();
                if ($errorExisteCodRenavam) {
                    $this->adicionaErroGenerico('Erro ao verificar RENAVAM: ' . $errorExisteCodRenavam);
                    $erroNaInsercao = TRUE;
                }
                if ($resultCodRenavam->num_rows) {
                    $row = $resultCodRenavam->fetch_assoc();
                    $cpfCnpjExistente = $row['NroCpfCnpj'];
                    if ($proprietario->getNroCpfCnpj() != $cpfCnpjExistente) {
                        $this->adicionaErroGenerico(htmlentities('RENAVAM ' . $value->getCodRenavam() . ' já cadastrado para cpf/cnpj diferente.', 2 | 0, 'ISO-8859-1', FALSE));
                        $erroNaInsercao = TRUE;
                    }   
                }

                $existePlaca = self::$SELECT_PLACA;
                $existePlaca = sprintf($existePlaca, "'" . $connection->realEscapeString($value->getPlaca()) . "'");
                $resultPlaca = $connection->executeQuery($existePlaca);
                $errorExistePlaca = $connection->getErrorNo();
                if ($errorExistePlaca) {
                    $this->adicionaErroGenerico('Erro ao verificar placa: ' . $errorExistePlaca);
                    $erroNaInsercao = TRUE;
                }
                if ($resultPlaca->num_rows) {
                    $row = $resultPlaca->fetch_assoc();
                    $cpfCnpjExistente = $row['NroCpfCnpj'];
                    if ($proprietario->getNroCpfCnpj() != $cpfCnpjExistente) {
                        $this->adicionaErroGenerico(htmlentities('Placa ' . $value->getPlaca() . ' já cadastrada para cpf/cnpj diferente.', 2 | 0, 'ISO-8859-1', FALSE));
                        $erroNaInsercao = TRUE;
                    }
                }                 

                if (!$errorExisteCodRenavam && !$errorExistePlaca && !$resultCodRenavam->num_rows && !$resultPlaca->num_rows) {
                    $codRenavam = $connection->realEscapeString($value->getCodRenavam());
                    $placa = $connection->realEscapeString($value->getPlaca());
                    $marca = $connection->realEscapeString($value->getMarca());
                    $modelo = $connection->realEscapeString($value->getModelo());
                    $ano = $connection->realEscapeString($value->getAno());
                    $cor = $connection->realEscapeString($value->getCor());
//                    $garagem = $connection->realEscapeString($value->getGaragem());
                    $tipoEndereco = $connection->realEscapeString($proprietario->getLogradouro()->getTipoEndereco());
                    $codLogradouro = $connection->realEscapeString($proprietario->getLogradouro()->getCodLogradouro());
                    $nomeLogradouro = $connection->realEscapeString($proprietario->getLogradouro()->getNomeLogradouro());
                    $numero = $connection->realEscapeString($proprietario->getLogradouro()->getNumero());
                    $complemento = $connection->realEscapeString($proprietario->getLogradouro()->getComplemento());
                    $bairro = $connection->realEscapeString($proprietario->getLogradouro()->getBairro());
                    $cep = $connection->realEscapeString($proprietario->getLogradouro()->getCep());
                    $dataHoraCadastro = date('Y-m-d H:i:s');
                    $tipoPessoa = $connection->realEscapeString($proprietario->getTipoPessoa());
                    $nroCpfCnpj = $connection->realEscapeString($proprietario->getNroCpfCnpj());
                    $nomeRzSocial = $connection->realEscapeString($proprietario->getNomeRzSocial());
                    $telefone = $connection->realEscapeString($proprietario->getTelefone());
                    $cartaoEntregue = $connection->realEscapeString($value->getCartaoEntregue());
                    $dataHoraCartao = $connection->realEscapeString($value->getDataHoraCartao());
                    $placaAnterior = NULL;
                    $cpfAprovador = NULL;
                    
                    $query = self::$INSERT_CADASTRO;
                    $query = sprintf($query, "'$codRenavam'", "'$placa'", "'$marca'", "'$modelo'", "$ano", "'$cor'", "'$dataHoraCadastro'", "'$tipoPessoa'", "$nroCpfCnpj", "'$nomeRzSocial'", "'$tipoEndereco'", "$codLogradouro", "'$nomeLogradouro'", "$numero", empty($complemento) ? 'NULL' : "'$complemento'", "'$bairro'", "$cep", empty($telefone) ? 'NULL' : $telefone, "'$cartaoEntregue'", empty($dataHoraCartao) ? 'NULL' : "'$dataHoraCartao'", "'$placaAnterior'", "'$cpfAprovador'");
                    $connection->executeQuery($query);
                    $errorNo = $connection->getErrorNo();
                    if ($errorNo) {
                        $this->adicionaErroGenerico(htmlentities('Erro ao cadastrar veículo: ' . $errorNo, 2 | 0, 'ISO-8859-1', FALSE));
                        $erroNaInsercao = TRUE;
                    }
                    else {
                        $this->adicionaMensagemGenerica(htmlentities('Veículo ' . $value->getPlaca() . ' cadastrado com sucesso.', 2 | 0, 'ISO-8859-1', FALSE));
                    } 
                    $resultCodRenavam->free();
                    $resultPlaca->free();
                } 
            }
            
            if (!empty($this->mensagensGenericas)) {
                $this->adicionaMensagemGenerica(htmlentities('Alterações das informações poderão ser  solicitadas a partir do dia 19/05/2014 diretamente na Secretaria Municipal de Segurança, na Av. Padre Cacique, 708, Praia de Belas', 2 | 0, 'ISO-8859-1', FALSE));
            }
  
            $connection->close();
            return $erroNaInsercao;    
        }

        public function getProprietarioExistente($post) {
            $connection = new \CopaControleVeiculos\Connection();

            $query = self::$SELECT_CADASTRO;
            
            $cpfCnpj = $post[self::getCampo('nroCpfCnpj')];
            $cpfCnpj = $connection->realEscapeString($cpfCnpj);
            $query = sprintf($query, $cpfCnpj);
            $result = $connection->executeQuery($query);
            
            $errorNo = $connection->getErrorNo();
            if ($errorNo) {
                $connection->close();
                $this->adicionaErroGenerico(htmlentities('Erro ao recuperar usuário existente: ' . $errorNo, 2 | 0, 'ISO-8859-1', FALSE));
                return NULL;
            }

            if (!$result->num_rows) {
                $result->free();
                $connection->close();
                return NULL;
            }                

            $proprietario = new \CopaControleVeiculos\Proprietario();
            while($row = $result->fetch_assoc()) {
                $proprietario->setNroCpfCnpj($row['NroCpfCnpj']);
                $proprietario->setTipoPessoa($row['TipoPessoa_CPF_CNPJ']);
                $proprietario->setNomeRzSocial($row['Nome_RzSocial']);
                $proprietario->setTelefone($row['Telefone']);                
                $proprietario->getLogradouro()->setTipoEndereco($row['TipoEndereco_R_C']);
                $proprietario->getLogradouro()->setCodLogradouro($row['CodLogradouro']);
                $proprietario->getLogradouro()->setNomeLogradouro($row['NomeLogradouro']);
                $proprietario->getLogradouro()->setNumero($row['Numero']);
                $proprietario->getLogradouro()->setComplemento($row['Complemento']);
                $proprietario->getLogradouro()->setBairro($row['Bairro']);
                $proprietario->getLogradouro()->setCep($row['CEP']);
 
                $veiculo = new \CopaControleVeiculos\Veiculo();
                
                $veiculo->setPlaca($row['Placa']);
                $veiculo->setCodRenavam($row['CodRenavam']);
                $veiculo->setMarca($row['Marca']);
                $veiculo->setModelo($row['Modelo']);
                $veiculo->setAno($row['Ano']);
                $veiculo->setCor($row['Cor']);
//                $veiculo->setGaragem($row['Garagem_S_N']);
                $veiculo->setCartaoEntregue($row['CartaoEntregue_S_N']);
                $veiculo->setDataHoraCartao($row['DataHoraCartao']);
            
                $proprietario->addVeiculo($veiculo);
            }
            
            $result->free();
            $connection->close();
            return $proprietario;
        }

        public function validaProprietarioExistente($post) {
            $this->erros = array();

            if (!$this->validaNroCpfCnpj($post)) {
                $this->setErro(self::getCampo('nroCpfCnpj'));
                $this->adicionaErroGenerico(htmlentities('CPF/CNPJ inválido.', 2 | 0, 'ISO-8859-1', FALSE));
                return 0;
            }

            return 1;
        }

        public function getUltimoConjuntoLogradouro($proprietario, $banco) {
            $logradouro = new \CopaControleVeiculos\Logradouro();
            $cpfCnpj = $proprietario->getNroCpfCnpj();

            if (!empty($cpfCnpj)) {
                if (!$banco) {
                       $veiculos = $proprietario->getVeiculos();
                        $veiculo = call_user_func('end', array_values($veiculos));
                        
                        $logradouro->setTipoEndereco($proprietario->getLogradouro()->getTipoEndereco());
                        $logradouro->setCodLogradouro($proprietario->getLogradouro()->getCodLogradouro());
                        $logradouro->setNomeLogradouro($proprietario->getLogradouro()->getNomeLogradouro());
                        $logradouro->setNumero($proprietario->getLogradouro()->getNumero());
                        $logradouro->setBairro($proprietario->getLogradouro()->getBairro());
                        $logradouro->setCep($proprietario->getLogradouro()->getCep());
                }
                else {
                    $connection = new \CopaControleVeiculos\Connection();
                
                    $query = self::$SELECT_ULTIMO_PROPRIETARIO;

                    $query = sprintf($query, $connection->realEscapeString($cpfCnpj));
                    
                    $result = $connection->executeQuery($query);
                    $errorNo = $connection->getErrorNo();
                    
                    if (!$errorNo) {
                        while ($row = $result->fetch_assoc()) {
                            $logradouro->setTipoEndereco($row['TipoEndereco_R_C']);
                            $logradouro->setCodLogradouro($row['CodLogradouro']);
                            $logradouro->setNomeLogradouro($row['NomeLogradouro']);
                            $logradouro->setNumero($row['Numero']);
                            $logradouro->setComplemento($row['Complemento']);
                            $logradouro->setBairro($row['Bairro']);
                            $logradouro->setCep($row['CEP']);
                        }
                    }
                    else {
                       $this->adicionaErroGenerico(htmlentities('Erro ao selecionar o último endereço do banco: ' .  $erroNo, 2 | 0, 'ISO-8859-1', FALSE));
                    }

                    if($result != NULL) {
                        $result->free();
                    }
                    $connection->close();
                }
            }
                
            return $logradouro;
        }

        public function setLogradouroPost(&$post, $logradouro) {
            $post[self::getCampo('tipoEndereco')] = $logradouro->getTipoEndereco();
            $post[self::getCampo('codLogradouro')] = $logradouro->getCodLogradouro();
            $post[self::getCampo('nomeLogradouro')] = $logradouro->getNomeLogradouro();
            $post[self::getCampo('numero')] = $logradouro->getNumero();
            $post[self::getCampo('complemento')] = $logradouro->getComplemento();
            $post[self::getCampo('bairro')] = $logradouro->getBairro();
            $post[self::getCampo('cep')] = $logradouro->getCep();
        } 

        public function limpaPostVeiculo(&$post) {
            $post[self::getCampo('codRenavam')] = '';
            $post[self::getCampo('placa')] = '';
            $post[self::getCampo('marca')] = '';
            $post[self::getCampo('modelo')] = '';
            $post[self::getCampo('cor')] = '';
            $post[self::getCampo('ano')] = '';
//            $post[self::getCampo('garagem')] = '';
        }

        public function adicionaErroGenerico($erro) {
            array_push($this->errosGenericos, $erro);
        }

        public function adicionaAlertaGenerico($alerta) {
            array_push($this->alertasGenericos, $alerta);
        }

        public function adicionaMensagemGenerica($mensagem) {
            array_push($this->mensagensGenericas, $mensagem);
        }

        public function reiniciaArrayGenerico($array) {
            $array = array();
        }

        public function arrayGenericoComoUl($array) {
            $ul = '<ul>';
            foreach($array as $value) {
                $ul .= '<li>' . $value . '</li>';
            }
            $ul .= '</ul>';
            $this->reiniciaArrayGenerico($array);
            return $ul;
        }

        public function errosGenericosComoDiv($session) {
            if (empty($this->errosGenericos)) {
                return '';
            }
            else {
                $div = '<div id="divErros" class="vermelho">';
                $div .= $this->arrayGenericoComoUl($this->errosGenericos);
                $div .= '</div>';
                return $div;
            }
        }

        public function alertasGenericosComoDiv($session) {
            if (!empty($this->errosGenericos)) {
                return '';
            }
    
            if ((!isset($session)) || ((isset($session) && !isset($session['iniciado'])))) {
                $alertaInicial = htmlentities('Informe o número do CPF ou do CNPJ da empresa e clique no botão "Avançar".', 2 | 0, 'ISO-8859-1', FALSE);
                $alertaInicial .= '<br>';
                $this->adicionaAlertaGenerico($alertaInicial);
            }
            else {
                $confirmado = FALSE;
                if (isset($session) && isset($session['proprietario']) && $session['proprietario'] == TRUE) {
                    $confirmado = TRUE;
                }
                $alertaConstante = '';
                if (!$confirmado) {
                    $alertaConstante = htmlentities('Preencha os dados do veículo e as informações pessoais, depois clique em "Avançar".', 2 | 0, 'ISO-8859-1', FALSE);
                    $alertaConstante .= '<br>';
                    $alertaConstante .=  htmlentities('Para cadastrar único veículo, clique no botão "Salvar".', 2 | 0, 'ISO-8859-1', FALSE);
                    
                }
                $alertaConstante .= htmlentities('Para cadastrar outro veículo, preencha os dados e clique no botão "Adicionar".', 2 | 0, 'ISO-8859-1', FALSE);
                $alertaConstante .= '<br>';
                $alertaConstante .= htmlentities('Você pode cadastrar quantos veículos necessitar.', 2 | 0, 'ISO-8859-1', FALSE);
                $alertaConstante .= '<br>';
                $alertaConstante .= htmlentities('Após o término clique no botão "Salvar" para efetivar o cadastramento.', 2 | 0, 'ISO-8859-1', FALSE);
                $alertaConstante .= '<br>';
                $alertaConstante .= '<br>';
                $alertaConstante .= htmlentities('Alterações das informações poderão ser solicitadas a partir do dia 19/05/2014 diretamente na Secretaria Municipal de Segurança, na Av. Padre Cacique, 708, Praia de Belas.', 2 | 0, 'ISO-8859-1', FALSE);
                $this->adicionaAlertaGenerico($alertaConstante);
            }

            if (empty($this->alertasGenericos)) {
                return '';
            }
            else {
                $div = '<div id="divAlertas" class="amarelo">';
                $div .= $this->arrayGenericoComoUl($this->alertasGenericos);
                $div .= '</div>';
                return $div;
            }
        }

        public function mensagensGenericasComoDiv($session) {
            if (empty($this->mensagensGenericas)) {
                return '';
            }
            else {
                $div = '<div id="divMensagens" class="verde">';
                $div .= $this->arrayGenericoComoUl($this->mensagensGenericas);
                $div .= '</div>';
                return $div;
            }
        }

        public function bairroComoHtmlSelect($post, $proprietario) {
            $bairro = '';
            
            
            if (!empty($proprietario) && $proprietario->isProprietarioConfirmado()) {
                
                $logradouro = $proprietario->getLogradouro();
                
                if (!empty($logradouro)) {
                    $bairro = $proprietario->getLogradouro()->getBairro();
                }
            }
            else {
                if (!empty($post[self::getCampo('bairro')])) {
                    $bairro = $post[self::getCampo('bairro')];
                }
            }   
     
            $htmlSelect = '';
            if (!empty($proprietario) && $proprietario->isProprietarioConfirmado()) {
                $htmlSelect = '<select id="logradouroBairro" name="logradouroBairro" disabled>';
//                $htmlSelect .= '<option value="none"></option>';
            }
            else {
                $htmlSelect = '<select id="logradouroBairro" name="logradouroBairro" >';
//                $htmlSelect .= '<option value="none" ' . ($bairro == '' ? 'selected' : '') . ' ></option>';
            }
            
            $htmlSelect .= '<option value="Menino Deus" ' . ($bairro == 'Menino Deus' ? 'selected' : '')  . ' >Menino Deus</option>';
            $htmlSelect .= '<option value="Praia de Belas" ' . ($bairro == 'Praia de Belas' ? 'selected' : '')  . ' >Praia de Belas</option>';
            $htmlSelect .= '<option value="Santa Tereza" ' . ($bairro == 'Santa Tereza' ? 'selected' : '')  . ' >Santa Tereza</option>';
            $htmlSelect .= '</select>';
            return $htmlSelect;
        }

        public function limitesComoHtmlSelect($post, $proprietario) {
            $codLogradouro = '';


            if (!empty($proprietario) && $proprietario->isProprietarioConfirmado()) {
                
                $logradouro = $proprietario->getLogradouro();
                
                if (!empty($logradouro)) {
                    $codLogradouro = $proprietario->getLogradouro()->getCodLogradouro();
                }
            }
            else {
                if (!empty($post[self::getCampo('codLogradouro')])) {
                    $codLogradouro = $post[self::getCampo('codLogradouro')];
                }
            }
            
            $htmlSelect = '';
            if (!empty($proprietario) && $proprietario->isProprietarioConfirmado()) {
                $htmlSelect = '<select id="logradouroCod" name="logradouroCod" onblur="loadCep()" disabled>\n';
//                $htmlSelect .= '<option value="none"></option>\n';
            }
            else {
                $htmlSelect = '<select id="logradouroCod" name="logradouroCod" onblur="loadCep()">\n';
//                $htmlSelect .= '<option value="none" ' . ($codLogradouro == '' ? 'selected' : '')  . ' ></option>\n';
            }

            $ultimoCod = 0;
            $selected = '';
            foreach (\CopaControleVeiculos\Limites::getLimitesArray() as $value) {
                if ($ultimoCod != $value->getCodLogradouro()) {
                    $ultimoCod = $value->getCodLogradouro();
                    if ($value->getCodLogradouro() == $codLogradouro) {
                        $selected = 'selected';
                    }
                    $htmlSelect .= sprintf('<option value="%s" %s>%s</option>\n', $value->getCodLogradouro(), $selected, htmlentities($value->getNomeLogradouro(), 2 | 0, 'ISO-8859-1', FALSE));
                }
                $selected = '';
            }
            $htmlSelect .= '</select>';
            
            return $htmlSelect;
        }

        public function cepValue($post, $proprietario) {
            $codLogradouro = '';

            if (!empty($proprietario) && $proprietario->isProprietarioConfirmado()) {
 
               $logradouro = $proprietario->getLogradouro();
    
               if (!empty($logradouro)) {
                    $codLogradouro = $logradouro->getCodLogradouro();
                } 
            }
            else {
                if (!empty($post[self::getCampo('codLogradouro')])) {
                    $codLogradouro = $post[self::getCampo('codLogradouro')];
                }
                else {
                    $codLogradouro = '7774136';
                }
            }

            switch($codLogradouro) {
                case '7774136':
                    return '90810240';
                case '7773054':
                    return '90810220';
                case '7774094':
                    return '90810230';
                case '7774110':
                    return '90810190';
                case '7774102':
                    return '98810210';
                case '7774128':
                    return '90850050';
                case '7774086':
                    return '90850110';
                default:
                    return '';
            }
        }

        public function veiculosJaSalvos($proprietario, $post) {
            $naoSalvos = '';
            
            $connection = new \CopaControleVeiculos\Connection();

            $veiculos = $proprietario->getVeiculos();

            foreach ($veiculos as $value) {
                $existeCodRenavam = self::$SELECT_CODRENAVAM_WHERE_CPF;
                $existeCodRenavam = sprintf($existeCodRenavam, $connection->realEscapeString($value->getCodRenavam()), $connection->realEscapeString($proprietario->getNroCpfCnpj()));
                $resultCodRenavam = $connection->executeQuery($existeCodRenavam);
                $errorExisteCodRenavam = $connection->getErrorNo();
                if ($errorExisteCodRenavam) {
                    $this->adicionaErroGenerico('Erro ao verificar RENAVAM: ' . $errorExisteCodRenavam);
                }
                if (!$resultCodRenavam->num_rows) {
                    if (empty($naoSalvos)) {
                        $naoSalvos .= $value->getPlaca();
                    }
                    else {
                        $naoSalvos .= ', ' . $value->getPlaca();
                    } 
                }
                $resultCodRenavam->free();
            }
            
            $connection->close();

            if (!empty($naoSalvos)) {
                return 'onclick="return confirm(\'O(s) veículo(s) ' . $naoSalvos . ' não foram salvos e serão perdidos. Deseja continuar?\');"';
//                return 'O(s) veículo(s) ' . $naoSalvos . ' não foram salvos e serão perdidos. Deseja continuar?';
            }
            else { 
                return '';
            }     
        }
  }

?>
