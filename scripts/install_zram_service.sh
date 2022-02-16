#!/usr/bin/env bash
echo "Configuring zram.service"
sudo touch /etc/modules-load.d/zram.conf
echo 'zram' | sudo tee /etc/modules-load.d/zram.conf
sudo touch /etc/modprobe.d/zram.conf
echo 'options zram num_devices=1' | sudo tee /etc/modprobe.d/zram.conf
sudo touch /etc/udev/rules.d/99-zram.rules
echo 'KERNEL=="zram0", ATTR{disksize}="2G",TAG+="systemd"' \
  | sudo tee /etc/udev/rules.d/99-zram.rules
sudo touch /etc/systemd/system/zram.service
echo "Installing zram.service"
cat << EOF | sudo tee /etc/systemd/system/zram.service
[Unit]
Description=Swap with zram
After=multi-user.target
[Service]
Type=oneshot 
RemainAfterExit=true
ExecStartPre=/sbin/mkswap /dev/zram0
ExecStart=/sbin/swapon /dev/zram0
ExecStop=/sbin/swapoff /dev/zram0
[Install]
WantedBy=multi-user.target
EOF
sudo systemctl enable zram
echo "You'll need to reboot for this to take effect."
