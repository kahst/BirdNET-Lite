# main v.11
- New "Reconfigure System" GUI
- labels.txt language support for 20+ languages
  - Tool in `birdnet-pi-config` for now
  - Added German top.html and menu.html

# main v.10 & pre-installed image notes
- New "BirdWeather" Chromium App (Pre-install image)
- New Infographics _chart_viewer.service_ (courtesy of @CaiusX)
- New "Overview"
- BirdWeather Support
- Bug Fix for systemd-networkd-wait-online.service
- Bug Fix for `install_noip2.sh` for NoIP DUC Support
- New `disk_check.sh` utitlity/crontab entry to `stop_core_services.sh`
  when disk space is greater than 95%

# main v0.9 -- pre-installed image
- Bug fix for Auto Access Point
- Improved Welcome Wizard
- Support for GPIO shutdown, reboot, and power on
- Ships with Caddy 2.4.5 to avoid 2.4.6 bug
- IceCast2 bug fix (for pre-installed image)

# main v0.8
- Supports Bullseye
- Pre-installed image has AutoHotSpot enabled
- Updated php from 7.3 to 7.4
- Updated MariaDB
- Added Configuration GUI

# newinstaller v0.7
- Systemd networkd supported

# newinstaller v0.5
- New `birdnet-pi-config` tool meant for:
  - SSH-only installation
  - Reconfiguring birdnet.conf
  - Configuring system settings
