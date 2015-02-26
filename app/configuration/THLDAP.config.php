<?php 

/**
 * LDAP.config.php
 * LDAP-Konfiguration
 * @author Robert Leonhardt
 */

## LDAP-Server
define( 'THLDAP_SERVER', '193.175.213.210' );
## LDAP-Port
define( 'THLDAP_PORT', 389 );
## String zum Authentifizieren von Benutzern
define( 'THLDAP_BIND_DN', 'uid={user},ou=users,dc=tfh-wildau,dc=de' );

## ROOT-Verzeichnis des LDAP-Baums
define( 'THLDAP_PATH_ROOT', 'dc=tfh-wildau,dc=de' );
## Mitarbeiter-Verzeichnis des LDAP-Baums
define( 'THLDAP_PATH_COWORKER', 'o=mitarbeiter,' . THLDAP_PATH_ROOT );

?>