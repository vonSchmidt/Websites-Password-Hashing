<?php

/**
 * A Common Cipher Algorithm widely used to
 * Register users inside a database in a
 * Relatively clean way.
 * 
 * A cipher module is first created using a
 * Message to cipher, and optionally a given
 * Salting string. In case it is not provided,
 * The module generates it and can return it
 * Using the get_ud_salt_base64() function.
 * Otherwise, the given salt is used to rehash
 * The text key, and that same salting will be 
 * returned.
 *
 * Use the encrypt_key() function to return
 * The final hash.
 *
 * Given a message MSG, this algorithm executes
 * The following procedure:
 * x = system_wide_salt .. MSG ;
 * // Where .. is a concatenation operator.
 * 
 * user_defined_salt is generated using 
 * /dev/urandom randomization method.
 *
 * x ..= binToBase64 ( user_defined_salt ) ;
 * 
 * return secure_key = sha256 ( x ) ;
 *
 * Use the generate_sha256_hmac_key() to return
 * an HMAC hash of the final hash.
 *
 * HMAC can ensure that a hash cannot be
 * exploited even if stolen, by assigning to
 * a given hash one constant secret key.
 *
 * System Administrator must store the secret
 * key in a place where it cannot be possibly 
 * reached in case of a database breach,
 * ie. another server or a different hard disk.
 *
 * @packaged default
 * @author zshulu
 **/

class cipher {

    private $secure_key;
    private $text_key;
    private $iv_size;
    private $user_defined_salt;
    private $reconstruct        = FALSE;
    private $HMAC_KEY           = 'YOUR_TOP_SECRET_KEY_HERE';

    public  $site_wide_defined_salt = 'whodoesnotlovesalt'; // Replace with a salting that suits your taste.

    public function __construct($textkey, $size = 16, $salt = '') {
        $this->iv_size = $size;

        if ($salt == ''){
            $this->user_defined_salt = 
                trim(
                    mcrypt_create_iv ( // Using /dev/urandom by default.
                        $this->iv_size
                    )
                );
            $this->reconstruct = FALSE;
        }
        else {
            $this->user_defined_salt = $salt;
            $this->reconstruct = TRUE;
        }

        $this->text_key = $textkey;
    }

    public function get_ud_salt_base64(){
        return 
            (!$this->reconstruct)?
            base64_encode (
                $this->user_defined_salt  // Transform salt from binary
            ) : $this->user_defined_salt; // Use provided salting as is.
    }


    public function encrypt_key(){
        $key = $this->site_wide_defined_salt .
            $this->text_key .
            $this->get_ud_salt_base64();

        $this->secure_key = trim (
            hash (
                'sha256',
                $key
            )
        );
        return $this->secure_key;
    }

    public function generate_sha256_hmac_key($i_pad=0x1c, $o_pad=0x2e){

        $key = $this->HMAC_KEY;
        $data = $this->secure_key;

        if(strlen($key) > $this->iv_size)
            $key = hash('sha256', $key);

        $key = str_pad($key, $this->iv_size, chr(0x00), STR_PAD_RIGHT);
        $i_key_pad = $o_key_pad = '';

        for($i = 0; $i < $this->iv_size; $i++){
            $i_key_pad .= chr(
                ord(
                    substr(
                        $key, $i, 1
                    )
                ) ^ $i_pad
            );

            $o_key_pad .= chr(
                ord(
                    substr(
                        $key, $i, 1
                    )
                ) ^ $o_pad
            );
        }

        return hash(
            'sha256',
            $i_key_pad . hash(
                'sha256',
                $o_key_pad . $data
            )
        );

    }

} // END CLASS Cipher

?>
