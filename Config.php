<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App;

class Config
{
    /** This module name. */
    const MODULE = self::MODULE_VENDOR . '_' . self::MODULE_PACKAGE;
    const MODULE_PACKAGE = 'App';
    const MODULE_VENDOR = 'Em34';

    const STOCK_ID_DEF = 1;
    const STORE_CODE_ADMIN = 'admin';
    const STORE_CODE_DEF = 'default';
    const STORE_ID_ADMIN = 0;
    const STORE_ID_DEF = 1;
    const WEBSITE_ADMIN = 0;
    const WEBSITE_DEF = 1;
}