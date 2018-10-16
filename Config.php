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
}