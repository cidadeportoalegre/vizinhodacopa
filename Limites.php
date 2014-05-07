<?php
    namespace CopaControleVeiculos;    
 
    require_once('Limite.php');
   
    class Limites {
        private static $LIMITES_ARRAY = null; 
            
        public static function getLimitesArray() {
            if (self::$LIMITES_ARRAY == null) {
                self::$LIMITES_ARRAY = array(
                    new \CopaControleVeiculos\Limite('7774136', 'Av. Padre Cacique', 'P', 20, 3146),
                    new \CopaControleVeiculos\Limite('7774136', 'Av. Padre Cacique', 'I', 619, 1567),
                    new \CopaControleVeiculos\Limite('7773054', 'Rua Monroe', 'I', 67, 181),
                    new \CopaControleVeiculos\Limite('7773054', 'Rua Monroe', 'P', 30, 164),
                    new \CopaControleVeiculos\Limite('7774094', 'Rua Otávio Dutra', 'P', 20, 214),
                    new \CopaControleVeiculos\Limite('7774094', 'Rua Otávio Dutra', 'I', 31, 231),
                    new \CopaControleVeiculos\Limite('7774110', 'Rua Dona Amélia', 'P', 18, 246),
                    new \CopaControleVeiculos\Limite('7774110', 'Rua Dona Amélia', 'I', 45, 239),
                    new \CopaControleVeiculos\Limite('7774102', 'Rua Gen. Oliveira Freitas', 'P', 30, 94),
                    new \CopaControleVeiculos\Limite('7774102', 'Rua Gen. Oliveira Freitas', 'I', 35, 95),
                    new \CopaControleVeiculos\Limite('7774128', 'Rua Miguel Couto', 'P', 158, 350),
                    new \CopaControleVeiculos\Limite('7774128', 'Rua Miguel Couto', 'I', 167, 355),
                    new \CopaControleVeiculos\Limite('7774086', 'Rua Barão do Cerro Largo', 'P', 10, 78),
                    new \CopaControleVeiculos\Limite('7774086', 'Rua Barão do Cerro Largo', 'I', 11, 93)
                );
            }
            return self::$LIMITES_ARRAY;
        }
    
        public static function verificaCodLogradouro($codLogradouro) {
            foreach(self::getLimitesArray() as $value) {
                if ($value->getCodLogradouro() == $codLogradouro) {
                    return 1;
                }
            }
            return 0;
        }

        public static function verificaNomeLogradouro($nomeLogradouro) {
            foreach(self::getLimitesArray() as $value) {
                if ($value->getNomeLogradouro() == $nomeLogradouro) {
                    return 1;
                }
            }
            return 0;
        }

        public static function getNomeLogradouro($codLogradouro) {
            foreach(self::getLimitesArray() as $value) {
                if ($value->getCodLogradouro() == $codLogradouro) {
                    return $value->getNomeLogradouro();
                }
            }
        }

        public static function verificaLimite($codLogradouro, $numero) {
            if ($numero % 2 == 0) {
                return self::verificaLimiteComLado($codLogradouro, 'P', $numero);
            }
            else {
                return self::verificaLimiteComLado($codLogradouro, 'I', $numero);
            }
        }

        public static function verificaLimiteComLado($codLogradouro, $lado, $numero) {
            foreach (self::getLimitesArray() as $value) {
                if ($value->getCodLogradouro() == $codLogradouro && $value->getLado() == $lado) {
                    if ($numero >= $value->getNumeroInicial() && $numero <= $value->getNumeroFinal()) {
                        return 1;
                    }
                }
            }
            return 0;
        } 
    }
?>
