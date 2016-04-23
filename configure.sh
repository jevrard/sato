#!/bin/bash
# sudo apt-get install zlib1g-dev
cd glucose-syrup/simp
make rs
./glucose_static --help
