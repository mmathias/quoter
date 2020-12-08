#!/usr/bin/env bash

sleep 10;
php bin/console messenger:consume -vv >&1;
