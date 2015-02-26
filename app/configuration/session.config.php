<?php 

/**
 * session.config.php
 * @author Robert Leonhardt
 */

## Session-Cookie Name (muss nicht unbedingt drauf hindeuten, dass DAS die Session-ID ist, ist sicherer ..)
define( 'SESSION_COOKIE', 'themehash' );
## Dauer einer Session
define( 'SESSION_DURATION', 60 * 60  );  // 1h

?>