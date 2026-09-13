<?php
/**
 * FixLine - Entry point for the Service Provider module.
 *
 * Usage:
 *   service_provider.php?action=dashboard   (default)
 *   service_provider.php?action=profile
 *   service_provider.php?action=apply_job
 *   service_provider.php?action=earnings
 *   service_provider.php?action=upload_picture   (AJAX/JSON only, POST)
 */

require_once __DIR__ . '/Controller/ServiceProviderController.php';