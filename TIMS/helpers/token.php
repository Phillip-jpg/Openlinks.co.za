<?php 
class token {
//bl means business logic

    static function get_unauth($bussiness_logic){
        $array = self::tokens();
        $int = random_int(0, count($array)-1);
        $secret = $bussiness_logic."unauthyasc2104";
        echo strval($int).hash_hmac('sha256', $secret, $array[$int]);
    }

    static function val_unauth($token, $bussiness_logic){
        
      
        $array = self::tokens();
        $secret = $bussiness_logic."unauthyasc2104";
        $index = array_search($token,$array);
        
        $calc =hash_hmac('sha256', $secret, $array[$token]);
        if (hash_equals($calc, substr($token, 1))) {
            return "TRUE";
        }else {
            $calc =hash_hmac('sha256', $secret, $array[$token]);
            if(hash_equals($calc, substr($token, 2)))
            {
                return "TRUE";
            }
            else{
                $calc =hash_hmac('sha256', $secret, $array[$token]);
                if(hash_equals($calc, substr($token, 3)))
                {
                    return "TRUE";
                }else{
                    return "FALSE";
                }
            }
        }
    }


    static function create_session_key(){// made in login
    $_SESSION['tokeny'] = bin2hex(random_bytes(32));
    }

    static function get($bussiness_logic){
    $id = self::ID();
    $secret = $bussiness_logic.strval($_SESSION[$id]);
    echo hash_hmac('sha256', $secret, $_SESSION['tokeny']);
    }
    static function get_ne($bussiness_logic){//no echo
        $id = self::ID();
        $secret = $bussiness_logic.strval($_SESSION[$id]);
        return hash_hmac('sha256', $secret, $_SESSION['tokeny']);
        }

    static function val($token, $bussiness_logic){
    
    $id = self::ID();
    $secret = $bussiness_logic.strval($_SESSION[$id]);
    $calc = hash_hmac('sha256', $secret, $_SESSION['tokeny']);
    if (hash_equals($calc, $token)) {
        return TRUE;
    }else {
        return FALSE;
    }
    }
    private static function ID(){
        if($_SESSION['WHO'] == "M_ADMIN" || $_SESSION['WHO'] == "G_ADMIN"){
            return "ADMIN_ID";
        }elseif($_SESSION['WHO'] == "NPO"){
            return "SMME_ID";
        }elseif($_SESSION['WHO'] == "P_COMPANY"){
            return "CONSULTANT_ID";
        }
        else{
            return $_SESSION['WHO']."_ID";
        }
    }
     private static function tokens(){
        $tokens = array(
            "9f801392880db7ed72f78ff36a2dad9a63e04c4c56481f3473d0a4d9b86fed2f",
            "20ffd68bb583115811312f27e9b7520171fcee4042fac95853e96d6574a694c7",
            "71c9b5f5ef7c3a7f43b9bdde8c367d50236a14e975e5b88861b69174de9bcc17",
            "1518ac336023692e03f358eb814fa1ef14245e41f0ba8c56a8c9ed8e2a509b0b",
            "c055672a687d733628c3ae352d7ef724e2dd770755aa5e39fb64da092b5eb760",
            "1c5c05951df7d0be74dd87d5c9bfb2f4526ae993e0fae3ec50b1c9cd1ab4adf8",
            "565326c7dbbba71428d981b37e9bab8306c75200a59474f7fcf945b07ef44748",
            "2813ac618161bd5819fa0f9bb1e0d3bd78818faaa59963baa485a4379b418af3",
            "c163a7dfaddf94fcd2295574c06297234cb0b4664012b63554107a5cecc06a1a",
            "c69b9e51acc0f8a5bc4bbf88d8f8c9a9ffe47d83ce36696cea3f249719ff04be",
            "f4d1cda35d9d23e759322f06a113de17a64aef563d593f98231d7069482bc74c",
            "14a7d0bda8b1af90156058f92d3499cff3ebb8b685ae235183bf63b5692ba45b",
            "812a7953d9dab4087acdd86505d82ab08840b93a970a62361e0facd1becadb29",
            "fd6e114dba6d4cc46867787f6002932388fbfbaad444d111c2301c9386f23b1e",
            "31d82ad6475395c15c2b5d74f3489e307072942dd5db1b012c64e5af30e8a45a",
            "84f5233b8e7d104e6f95330e4287043953d7513aa24271fe5a7858ba844c3e87",
            "93aad932118b4073d53a026015b7a52c4fc63235a00c6da23f829fcae874c1ac",
            "aa59f22b538a6fee1463482ec78d655e9f41932e250929a609f794936c652afc",
            "2aa9a04854908e587b1229175e3f37301161ff73eaff089e2ff349c48c2d4035",
            "861a5787815d8e372258f3c41c2f2e9bac6b607dd185b7580dfefb220fe254f4",
            "95f46e6a90507b19fa76a6702a359b513b96bc62689474ed55646c5b1118a18c",
            "c3fa01c883ad2ac85653da7b83fda92bd4cef4b65f43552c31b5264165640bd0",
            "127857f23f8d4b0b1bea7c2f7041f0d0f31cad66374d64ba2f08e828a4ebe6f1",
            "3d653bf92a12c2653f3bc1b6735d9f36a2bd5be155c02dc0fac8757b0469dd94",
            "c540d1f5f9a9eeb7807a31b43990482429869764aedcb85415baa87ad8177bde",
            "f15e64467ff7da937bf365ac454847ffe8aeedb52eaf97683c246d6eda999e2b",
            "f44ac2140ebf24de58c8d676ac89b09ae90b51c90cc7f2e9423046ab427dbcea",
            "acc6a29aba2cef9ea97e0afb407d06799cda8fe74b8094635ff4d3930448c6d7",
            "449f20d6c9e626de84ebb4f07d6c121bf453e106f4c8d62af3799d8fc402d043",
            "694cfda5cf46cc12a2a873f294ecda79232331b819f173a47050e9b8ef3a931a",
            "2937f8220c88ccdcc3ed27119b5216cf44e241d61d59f5f939eea14edb37ab1f",
            "43a9fdfc9468f0f58664bb6c0de5affc77d35e2746b15f30932ba5699e5fb35c",
            "5738a5c58011e3dfe5c66602027bd37ca4573666e9c471c4c440a3c5477ddaa9",
            "3a9a5afec068aec772f2b1cbd3fc73505f42bd2b6a7655755b6147f8f876f564",
            "13d9c58f64a1cbad50d5e31b6f282f9d5f28c1226cbf36e6750946bd86da1212",
            "c21f0b333f56cc6bea7d89611a504436d6784cd98665593ce9c615ae49361507",
            "d493ddc986c1fd3f32575922af52605a19fb997f5eba9af4382375f59e6bcacf",
            "1c1e336560d8ed80a9158cdecd26278b12eb09022af50c0a61766c3dc51573ab",
            "7305548e1459c7e4e2ad7bce213f5803dde651bdf3f570b55eba5b83b5fa67ec",
            "3b8bc28f1b0735e5eac6c321b3d031f43d2a8f67e932bee79b1d8d5bc7484093",
            "04991322b074048d4d945c55287bd08d49ca10a30a6a4e28941392392279e2a9",
            "a30eb4b7463a5ca1b589cb5e82b72ba8e10ff1d7d2aafbfdc995f2b918abcc5e",
            "ea284b2836e08ad24d9e157f3a586c0b5d15268257384ab2cf5a3199b81686f9",
            "2a91bce6b8f3a53edccd77356a0eba196f923e672c291baf1ae0cfba39e119e9",
            "b70bdc090a387d99780a29df3b65dce9894f61c33b7cb4598d484223c9f6b4f2",
            "2fef0f4bb3e13cb1d32c327813f44e72428636c070804a761b0016510a6bdf04",
            "f51ed1e94d7f84b66d801adae6d3381602219978ecd8b6697a83a04006e8649b",
            "f20254eb355da038c3cf03b69bb0bfb5384ce9f41c099cd7321c49d8e9ba06f0",
            "eddce1bc2bdb6e12760511ed2683263f37f47d6094c1edead643ce45df127c7b",
            "8365251e521890e16f6d162701fa012b23e2976b441ac867809ab2fe253a3785",
            "497a99f1f4b20de05422056f71ec671b196b724d95433a3b1bafe10df6e4c648",
            "364c324613024e429a21df6eb333c10f4d67146c1f8c08ac2340373e4b3f244b",
            "dd811ba0cc34bbf1780742bc490e3add92ecec915f99a9cc09ece2d9e0d04228",
            "a819ec8a569d2f7e059d00ced3fa5ac7105c2e7c2801d059b024218cdd704eed",
            "7ab4ca70d2fa025fdfa3cb2835e01f1eab9054064789fc4d21632817ccd4c389",
            "a0503b3381150c9222c14e6928ba103b5d6aea50c13e16386468a02a931790af",
            "410527aa8c568c01bb64d5bdddd95e6ae1051629909208888fe54477d71630aa",
            "2a871d3b031b48dec51fb780e1ed7b4a67b68e21e7f7aa1b4b51535556414ec9",
            "c71f51d26826d627ae6533acb01e0b6b588eb85a14398f3eb9708e817d51e2bb",
            "83517cdfb743fdd4d0bec83047d9d87093dfb18b621210463489aff9b40287d1",
            "3f244f4404d9496b288c9a3599fef853109316a6a17e576ec745eaac3dff21ea",
            "6e79ae17e748c957e2247187149dc5c8d0657b596f49aea0ba6c9e8f42b55417",
            "6329f9126ff0bee4c94ebfb46fcc8ccce80c629b8a12990c01089bce36bb782d",
            "644c793dbdd4e597a0a2b4e86dbae5a98c30c793f675f7106e99b36dbf981f3d",
            "50e807e2e7f528ac5142f4bc26a4cbf354ae676856d1a91bb5e4068a266c0580",
            "bb0425a88c973d6407e0d61567d1d7506e3eae30a0b38ee995eca597f720560d",
            "239253365afb50d7e657cbb254a2e42b0e9269a9c458316ad5e84b23ec4516a7",
            "992c2e30cee447409cb7b7450850980a98c60b7f683afc4f8252215858e8e56b",
            "9d37661df0b7ac1f71ad0199b301609d3f51e67f6730b6aaca4fb9b0d32dd8a5",
            "e39c9fcb1c864deb01b288c4e459e2f81136dfaef29edcf22dae475fc2be2b44",
            "4303263c0344c4421615612340e0a803088027b3e2d6c59b5de9b89a2cb7f3da",
            "9147e36f0408e977b1e5b39a3c89a51165cee56521bbb78b1eef5c9f2064f1b1",
            "6d3afb447e0e03d4b8d402e43c42a90e417924216ec981696bf49443efc1c972",
            "3882891b74c8bbb2fb436fbb1f220a044da234fa791a1ad29373e8a13a1d3f10",
            "35ec5884637b864d61a9df957b7ccb2849070d4738c091cc1d837516dd04b8fe",
            "ed5c8b0a61c264d36152a7a007a2ee9452cd799e51bcdbb0fa25f550e84380d4",
            "edada81ac15df21737ebca6597241c073c6b26214cc063536b782ae93e893a58",
            "ae53c33c6e2e4276a8dbbe66ac22abd4d60431645943aebb75c07be032736686",
            "eb84f52b58b0f6e96610b3bbbf25d8b7c7b081f045d51c60ff3f98ae0d1cb0a0",
            "936f55e9b13d2f92c435eba62f81238e772c1f405d9658cc0ec3c57bd131262d",
            "4d32ec50afad6cfc46a87400c61a349b7dcd58b7d4582f5e3fe54657e368358a",
            "4353dd35a2f3fb722ee02863f0ecd13d40e1b33a4a853c22bff902123245b902",
            "39260798eaf758e60ed475eafac52364d63ea37ed707d873a1edd01f6326f66f",
            "7c5408377f1eb58f394cf3554ecaa368cc3efdacabd9508a1335b025e535bd20",
            "62e4d028cf0c2a5289e9b8b5fb3a7ae66fefd68952a21c51d9f4b49dbcfd544a",
            "bdc5850689c116c258a526fa8bfe2cb04b7647df0e3dc84ec79618a545800ab4",
            "bdf626e803bc7a606dbfa01109fb80b6c9dacdee2f1c04217e91ba0411b1f1ec",
            "373017963093638825bf6745592138bd44b48cb5137c7554ef83b74f0f4c7503",
            "2d6389424d7ee87e81e1def96759462deb4a28770a29efed267583d0bb76e7f2",
            "43a42ed2b3396688b6793940a8881e72f386a48af5726700ac30ad09dcf36760",
            "6a21a237d56fe0d63e0dd90cec22851470359c6fcc2448370844ec6916e80cf8",
            "c8493880ce75676069f98b3d223ff179589618e56cb8a974b8d23bfff9c612ae",
            "973274a23fbd8115504198a6e1b5ff212ba7ee780746942dc384dd6814ee6f6c",
            "a3a473fecefb724702c8caaceb295b2743b13039186779879b9ad8e2d1c21b09",
            "8998cffbefc9fde797e7c86e51dd748ad46508dc54e8a1ee60be3ac82204123a",
            "c31ba7a64b3217e901897871a894cc061457b15dfdd3dd41e25968547f2200f7",
            "45bfb4a21996364ed9269dc0dc4a3331df35e6f501165d28f37f4a44ca340db0",
            "48180ccc03b7b0b566fe005521bcacc0d2ce65d3c9798321aaf61d91ceeed31e",
            "76a03c687639cb6f103aa51d21fe0ee3d55bae0bfbb7de499a8321736d2bf62b",
            "d1b49dcbbbc06ae9388dc4decb883ab3cb80dd6857586c1ff7bebc48b8032b42",
            "aa1ae990e3481f307d674fc35785c7b9abd803c29d86a977d9dba0b6847fcfb1",
            "eda7134341e88e57dc4893e3e72c704399a43674678812172d61ad7cfe06501a",
            "eb6b3684a6291e3832e266fd3da713a15aba2c2f91bb1c92126d1a030bf58d7e",
            "d6f6f5a5b6b6a920c871cf36b9748f1b20cd9657addee4cb062cf95e6ad8e8f4",
            "f982bcf86c603d1aa729c5d6ad4a66804f9cc5b904b66c9ef4ceda394818b7ab",
            "45f135a9c6bfe2e11f7ff07a462a2ed54aebb68eb97133d1b99b658397683f61",
            "8c2c26485d02110bf57528f7cc85cb91f67cee1048b71e25a7a531ccdec05aef",
            "e43708ccb23cab55cc50913e0ed6f76594b063cfbcbc283c3060b6d5d91f7eb3",
            "ce4c4bd7869d108b18a669e493c7bd5f3bfb92e9c14188b973fca3bcea1290d3",
            "ee26fac22c22abf97d3380da4d51d1a1f9e743b56074309a5c63a2116e4b5e79",
            "567e818204dc3ab0c8f7e6a61b94d3ae6e9f3494c848d6a349936d90a6e26869",
            "5df5a96c722405fc3f83da314ea3eb2581a6bc24e2bfef6d1e591f55a9f05e60",
            "bd942af4e844304c833766e90aab14feae7e85728a50620e2076c9a6fcde39cb",
            "7f830cfa6c75fb8d4f52ee95d5d25891aab7b69727041ab246fcbbd7a4bf755e",
            "d85d5a8a98ea384d724cff6175fa036fd8f0b03932820dff2a26a1a358642254",
            "3bb3bed87e069af07012e1ab45fb0d6f7a1297c60ccf380feab233f1c17a4bde",
            "a985a3068c8e38c4a699f8acfc46bd9bad301ee9249c1f525883123e592317fe",
            "ce1ced863ab2ec00c83ea64df8b120930d849247f009aac1729e79d7f87f014e",
            "06c7814194351896af0107c1d55474590c03f76809ac04b6d79779acee0b0344",
            "b9be3c8491ffebc70155f177853c70f8f5b011305c7af18faf93770c86abf4ae",
            "ab76d0075b54bcfad32e80e08cc9844b5d3d3b0205c03fa89ddc0a4f742cc6f8",
            "2404b76b1a66cf4b62d22afe240bb215183696ee28a0da5b6f2a8c73babbcd13",
            "13182c4287feaf88a4755c255b60501ee54605e02bcc262f916bf25eef61b6db",
            "4afcbcbc572e7089e3a259583a79ba5390e00fcf48e18210b4423fadf353e03c",
            "e73b8ee1a87194e6be404f4f74e255e68df584d4d45719279c104c5935dc2afd",
            "678636f0c518dab124f0447ed45ef142e6d8a567ed12c02aa988acd755a3622b",
            "16eaa42a2902f7afae8c87ea0a730001692731e69b312868ee0c3c780e90bf96",
            "8a8432a60d2a8a4588a9c9af9199e34b2a22b5c29fc5f80766a7fdfb6d98d0cd",
            "13d329553f175ade3e2bb56dc89ffe33698691e0b19c36682d5c77e8c3c210c5",
            "0501e4e1d6062d335d3e898f0c66b268b008e183ebba4d49873e99982a03abc9",
            "4e3a62f4aeab8e22ed2459591540995d3494329448eff254454ccf03398fcd0a"
        );
        return $tokens;
    }

    static function encode1($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
      }
      
      static function decode1($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
      }

    private static function safe_b64encode($string='') {
        $data = base64_encode($string);
        $data = str_replace(['+','/','='],['-','_',''],$data);
        return $data;
    }

    private static function safe_b64decode($string='') {
        $data = str_replace(['-','_'],['+','/'],$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

     static function encode($value){ 
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($iv_size);
        $crypttext = openssl_encrypt($value, 'aes-256-cbc', '16eaa42a2902f7afae8c87ea0a730001692731e69b312868ee0c3c780e90bf96d6f6f5a5b6b6a920c871cf36b9748f1b20cd9657addee4cb062cf95e6ad8e8f4', OPENSSL_RAW_DATA, $iv);
        return self::safe_b64encode($iv.$crypttext); 
    }

 static function decode($value){
        $crypttext = self::safe_b64decode($value);
        $iv_size = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($crypttext, 0, $iv_size);
        $crypttext = substr($crypttext, $iv_size);
        if(!$crypttext) return false;
        $decrypttext = openssl_decrypt($crypttext, 'aes-256-cbc', '16eaa42a2902f7afae8c87ea0a730001692731e69b312868ee0c3c780e90bf96d6f6f5a5b6b6a920c871cf36b9748f1b20cd9657addee4cb062cf95e6ad8e8f4', OPENSSL_RAW_DATA, $iv);
        return rtrim($decrypttext);
    }

      static function ip() {  
        //whether ip is from the share internet  
         if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                    $ip = $_SERVER['HTTP_CLIENT_IP'];  
            }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
         }  
        //whether ip is from the remote address  
        else{  
                 $ip = $_SERVER['REMOTE_ADDR'];  
         }  
         return $ip;  
    }
}