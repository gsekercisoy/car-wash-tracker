package com.otoyikama.api.security;

import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Component;

/**
 * PHP password_hash() (bcrypt, $2y$ one) ile uretilmis sifreleri dogrular.
 * Yeni sifreler BCrypt ile hashlenir ($2a$), PHP password_verify() tarafindan okunabilir.
 */
@Component
public class PhpPasswordEncoder implements PasswordEncoder {

    private final BCryptPasswordEncoder delegate = new BCryptPasswordEncoder();

    @Override
    public String encode(CharSequence rawPassword) {
        return delegate.encode(rawPassword);
    }

    @Override
    public boolean matches(CharSequence rawPassword, String encodedPassword) {
        if (encodedPassword != null && encodedPassword.startsWith("$2y$")) {
            encodedPassword = "$2a$" + encodedPassword.substring(4);
        }
        return delegate.matches(rawPassword, encodedPassword);
    }
}
