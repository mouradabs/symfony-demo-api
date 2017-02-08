keys:
	openssl genrsa -passout pass:secret -out var/jwt/private.pem -aes256 4096
	openssl rsa -passin pass:secret -pubout -in var/jwt/private.pem -out var/jwt/public.pem
