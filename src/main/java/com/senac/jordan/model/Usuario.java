package com.senac.jordan.model;

import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "Usuario")
@Getter
@Setter
public class Usuario {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    
    @Column(nullable = false, unique = true)
    private String email;
    
    @Column(nullable = false)
    private String senha;
    
    @Enumerated(EnumType.STRING)
    private Grupo grupo;
    
    private Integer status; // 0 = Inativo, 1 = Ativo
    
    private String nome;
    
    @Column(unique = true, length = 14)
    private String cpf;
}
