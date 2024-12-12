#!/bin/bash

echo "SMTP: $SMTP"
echo "SMTP_PORT: $SMTP_PORT"
echo "SMTP_GMAIL_USER: $SMTP_GMAIL_USER"
echo "SMTP_GMAIL_PASS: $SMTP_GMAIL_PASS"

if [[ -z "$SMTP" || -z "$SMTP_PORT" || -z "$SMTP_GMAIL_USER" || -z "$SMTP_GMAIL_PASS" ]]; then
  echo "ERROR: one or more config undefined!"
  exit 1
fi

envsubst < /etc/msmtprc.template > /etc/msmtprc

echo "Generated msmtprc file:"
cat /etc/msmtprc
