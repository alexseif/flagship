#!/bin/bash
# Deployment Cleanup Script for Legacy Plugins & Tables

echo "Deactivating and uninstalling legacy plugins (this triggers native cleanup)..."
wp plugin deactivate js_composer LayerSlider revslider
wp plugin uninstall js_composer LayerSlider revslider --deactivate

echo "Dropping any remaining orphaned tables..."
wp db query "DROP TABLE IF EXISTS wp_layerslider, wp_layerslider_revisions, wp_nextend2_smartslider3_generators, wp_nextend2_smartslider3_sliders, wp_nextend2_smartslider3_sliders_xref, wp_nextend2_smartslider3_slides, wp_revslider_navigations, wp_revslider_static_slides;"

echo "Cleanup complete."
