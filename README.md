#PHP Cipher
============
A Common Cipher Algorithm widely used to
Register users inside a database.

A cipher module is first created using a
Message to cipher, and optionally a given
Salting string. In case it is not provided,
The module generates it and can return it
Using the get\_ud\_salt\_base64() function.
Otherwise, the given salt is used to rehash
The text key, and that same salting will be 
returned.

Use the encrypt\_key() function to return
The final hash.

##Procedure
Given a message MSG, this algorithm executes
The following procedure:
x = system\_wide\_salt .. MSG ;
// Where .. is a concatenation operator.

user\_defined\_salt is generated using 
/dev/urandom randomization method.

x ..= binToBase64 ( user\_defined\_salt ) ;

return secure\_key = sha256 ( x ) ;

Use the generate\_sha256\_hmac\_key() to return
an HMAC hash of the final hash.

HMAC can ensure that a hash cannot be
exploited even if stolen, by assigning to
a given hash one constant secret key.

_NOTE_
System Administrator must store the secret
key in a place where it cannot be possibly 
reached in case of a database breach,
ie. another server or a different hard disk.


##Manual

> $cipher = new cipher('Message');
> $key    = $cipher-&gt;encrypt\_key();
> $salt   = $cipher-&gt;get\_ud\_salt\_base64();
> $hmac   = $cipher-&gt;generate\_sha256\_hmac\_key();
