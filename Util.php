<?php
    namespace CopaControleVeiculos;    

    class Util {
        
        private static $CPF_INVALIDO = array(00000000000,
                                                   11111111111,
                                                   22222222222,
                                                   33333333333,
                                                   44444444444,
                                                   55555555555,
                                                   66666666666,
                                                   77777777777,
                                                   88888888888,
                                                   99999999999,
                                                   00000000191);
        private static $CNPJ_INVALIDO = array(11111111111111,
                                              22222222222222,
                                              33333333333333,
                                              44444444444444,
                                              55555555555555,
                                              66666666666666,
                                              77777777777777,
                                              88888888888888,
                                              99999999999999);    
        private static $PRIMEIRO_DIGITO = 1;
        private static $SEGUNDO_DIGITO = 2;
        private static $CNPJ_MULTIPLICADORES_PRIMEIRO_DIGITO = array(5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
        private static $CNPJ_MULTIPLICADORES_SEGUNDO_DIGITO = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
        private static $CPF = 1;
        private static $CNPJ = 2;
    
        public static function validaCpfCnpj($str) {
            if (strlen($str) == 11) {
                return self::validaCpf($str);
            }
            if (strlen($str) == 14) {
                return self::validaCnpj($str);
            }
            return 0;         
        }

        public static function validaCpf($cpf) {
            if (!self::verificaCpfInvalido($cpf)) {
                return 0;
            }
            
            $digitosVerificadores = self::digitosVerificadores($cpf);

            if ($digitosVerificadores[0] != self::calculaDigitoVerificador(self::calculaSomatorio($cpf, self::$PRIMEIRO_DIGITO, self::$CPF))) {
                return 0;
            }

            if ($digitosVerificadores[1] != self::calculaDigitoVerificador(self::calculaSomatorio($cpf, self::$SEGUNDO_DIGITO, self::$CPF))) {
                return 0;
            }

            return 1;
        }
        
        public static function verificaCpfInvalido($cpf) {
            foreach (self::$CPF_INVALIDO as $value) {
                if ($value == $cpf) {
                    return 0;
                }
            }
            return 1;
        }

        public static function semDigitosVerificadores($cpf) {
            return substr($cpf, 0, -2);
        }
        
        public static function semUltimoDigitoVerificador($cpf) {
            return substr($cpf, 0, -1);
        }

        public static function digitosVerificadores($str) {
            return substr($str, -2, 2);
        }
        
        public static function digitoVerificador($str) {
            return substr($str, -1, 1);
        }

        public static function calculaSomatorio($str, $digito, $documento) {
            $strSemAlgumDosDigitos = 0;

            if ($digito == self::$PRIMEIRO_DIGITO) {
                $strSemAlgumDosDigitos = self::semDigitosVerificadores($str);
            }
            
            if ($digito == self::$SEGUNDO_DIGITO) {
                 $strSemAlgumDosDigitos = self::semUltimoDigitoVerificador($str);
            }
            
            if ($documento == self::$CPF) {
                return self::cpfCalculaSomatorio($strSemAlgumDosDigitos, $digito);
            }

            if ($documento == self::$CNPJ) {
                return self::cnpjCalculaSomatorio($strSemAlgumDosDigitos, $digito);
            }
        }
        
        public static function cpfCalculaSomatorio($cpf, $digito) {
            $multiplicador = 0;

            if ($digito == self::$PRIMEIRO_DIGITO) {
                $multiplicador = 10;
            }

            if ($digito == self::$SEGUNDO_DIGITO) {
                $multiplicador = 11;
            }
            
            $somatorio = 0;
            
            for ($i = 0; $i < strlen($cpf); $i++) {
                $somatorio += $cpf[$i] * $multiplicador;
                $multiplicador--;
            }
        
            return $somatorio;            
        }
       
        public static function cnpjCalculaSomatorio($cnpj, $digito) {
            $multiplicadores = array();

            if ($digito == self::$PRIMEIRO_DIGITO) {
                $multiplicadores = self::$CNPJ_MULTIPLICADORES_PRIMEIRO_DIGITO;
            }

            if ($digito == self::$SEGUNDO_DIGITO) {
                $multiplicadores = self::$CNPJ_MULTIPLICADORES_SEGUNDO_DIGITO;
            }

            $somatorio = 0;
            
            for ($i = 0; $i < strlen($cnpj); $i++) {
                $somatorio += $cnpj[$i] * $multiplicadores[$i];
            }

            return $somatorio;
        }
     
        public static function calculaDigitoVerificador($somatorio) {
            $resto = $somatorio % 11;

            if ($resto < 2) {
                return 0;
            }
            else {
                return 11 - $resto;
            }
        }

        public static function validaCnpj($cnpj) {
            if (!self::verificaCnpjInvalido($cnpj)) {
                return 0;
            }

            $digitosVerificadores = self::digitosVerificadores($cnpj);


            if ($digitosVerificadores[0] != self::calculaDigitoVerificador(self::calculaSomatorio($cnpj, self::$PRIMEIRO_DIGITO, self::$CNPJ))) {
               return 0;
            }
            if ($digitosVerificadores[1] != self::calculaDigitoVerificador(self::calculaSomatorio($cnpj, self::$SEGUNDO_DIGITO, self::$CNPJ))) {
                return 0;
            }
            
            return 1;
        }

        public static function verificaCnpjInvalido($cnpj) {
            foreach(self::$CNPJ_INVALIDO as $value) {
                if ($cnpj == $value) {
                    return 0;
                }
            }
            
            return 1;
        }        
        
        public static function validaRenavam($renavam) {
            if (strlen($renavam) != 11) {
                return 0;
            }
            
            $digitoVerificador = self::digitoVerificador($renavam);

            if ($digitoVerificador != self::calculaDigitoVerificador(self::calculaSomatorio($renavam, self::$SEGUNDO_DIGITO, self::$CPF))) {
                return 0;
            }
            
            return 1;           
        }

        public static function cpfRegex($str) {
            if (!preg_match("/^[0-9]{1,11}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function cnpjRegex($str) {
            if (!preg_match("/^[0-9]{1,14}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function cepRegex($str) {
            if (!preg_match("/^[0-9]{8}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function numeroRegex($str) {
            if (!preg_match("/^[0-9]{1,5}$/", $str)) {
                return 0;
            }
            return 1;
        }
       
        public static function nomeRegex($str) {
                if (!preg_match("/^.{1,70}$/", $str)) {
                return 0;
            }
            return 1;
        }

           
 
        public static function bairroRegex($str) {
            if (!preg_match("/^.{1,30}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function complementoRegex($str) {
            if (!preg_match('/^.{0,30}$/', $str)) {
                return 0;
            }
            return 1;
        }
        
        public static function placaRegex($str) {
            if (!preg_match("/^[a-zA-Z]{3}[0-9]{4}$/", $str)) {
                return 0;
            }
            return 1;
        }
        
        public static function renavamRegex($str) {
            if (!preg_match("/^[0-9]{1,11}$/", $str)) {
                return 0;
            }
            return 1;
        }
        
        public static function marcaRegex($str) {
            if (!preg_match('/^.{1,30}$/', $str)) {
                return 0;
            }
            return 1;
        }

        public static function anoRegex($str) {
            if (!preg_match("/^[0-9]{4}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function corRegex($str) {
            if (!preg_match("/^[a-zA-Z\sãâáàéêíôóúü]{1,15}$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function telefoneRegex($str) {
            if (!preg_match("/^(([0-9]{2})?[0-9]{8})*$/", $str)) {
                return 0;
            }
            return 1;
        }

        public static function iniciaSessao() {
            session_start();
        }

        public static function finalizaSessao() {
            $_SESSION = array();
            $_POST = array();
            
            session_destroy();
            
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 36000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
        }

        public static function insertPad($str, $size) {
            if (strlen($str) >= $size) {
                return $str;
            }

            $pad = NULL;
            if (strlen($str) < $size) {
                for ($i = 0; $i < $size - strlen($str); $i++) {
                    $pad .= '0';
                }
            }
            return $pad . $str;
        }
    }
?>
