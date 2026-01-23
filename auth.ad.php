<?php
function autenticarUsuarioAD($usuario, $password) {
    $modo_desarrollo = false; 

    $usuario_dev = 'admin';
    $password_dev = 'admin123';

    if ($modo_desarrollo) {
        if ($usuario === $usuario_dev && $password === $password_dev) {
            return ['success' => true, 'msg' => 'Login (Modo Desarrollo)'];
        }
        return ['success' => false, 'msg' => 'Credenciales incorrectas (Modo Desarrollo)'];
    }

    $ldap_host = "192.168.100.182";        // IP de Windows Server
    $ldap_dominio = "biblioteca.local"; // dominio
    $ldap_base_dn = "dc=biblioteca,dc=local"; // La raíz donde buscar usuarios
    $grupo_requerido = "Bibliotecarios"; //

    $ldap_conn = ldap_connect($ldap_host);
    
    if (!$ldap_conn) {
        return ['success' => false, 'msg' => 'Error: No se pudo conectar al servidor AD (' . $ldap_host . ')'];
    }

    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

    try {
        $ldap_user_principal = $usuario . "@" . $ldap_dominio;

        $bind = @ldap_bind($ldap_conn, $ldap_user_principal, $password);

        if (!$bind) {
            return ['success' => false, 'msg' => 'Usuario o contraseña incorrectos en Windows.'];
        }

        $filter = "(sAMAccountName=$usuario)";
        $attr = array("memberof");
        
        $result = ldap_search($ldap_conn, $ldap_base_dn, $filter, $attr);
        $entries = ldap_get_entries($ldap_conn, $result);

        $es_admin = false;

        if (isset($entries[0]['memberof'])) {
            foreach ($entries[0]['memberof'] as $grupo) {
                if (strpos($grupo, $grupo_requerido) !== false) {
                    $es_admin = true;
                    break;
                }
            }
        }

        ldap_unbind($ldap_conn);

        if ($es_admin) {
            return ['success' => true, 'msg' => 'Bienvenido, Bibliotecario.'];
        } else {
            return ['success' => false, 'msg' => 'Acceso Denegado: Tu usuario es válido, pero no tienes permisos de Administrador.'];
        }

    } catch (Exception $e) {
        return ['success' => false, 'msg' => 'Error de excepción LDAP: ' . $e->getMessage()];
    }
}
?>