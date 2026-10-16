package com.otoyikama.api.security;

import com.otoyikama.api.entity.User;
import com.otoyikama.api.repository.UserRepository;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

@Service
public class CustomUserDetailsService implements UserDetailsService {

    private final UserRepository users;

    public CustomUserDetailsService(UserRepository users) {
        this.users = users;
    }

    @Override
    public UserDetails loadUserByUsername(String username) throws UsernameNotFoundException {
        User u = users.findByKullaniciAdi(username)
                .orElseThrow(() -> new UsernameNotFoundException("Kullanici bulunamadi: " + username));
        return org.springframework.security.core.userdetails.User
                .withUsername(u.getKullaniciAdi())
                .password(u.getSifre())
                .roles(u.getRol()) // -> ROLE_admin / ROLE_personel
                .disabled(u.getAktif() == null || !u.getAktif())
                .build();
    }
}
