<?php
/**
 * Redirecionamento de conveniência: acessar /atendelab/ leva para /atendelab/public/.
 */
header('Location: public/?controller=auth&action=login');
exit;
