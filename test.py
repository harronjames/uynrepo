import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

# Brevo SMTP Bilgileri
smtp_server = "smtp-relay.brevo.com"
smtp_port = 587
smtp_user = "b4726a001@smtp-brevo.com" # Brevo giriş e-postan
smtp_pass = "xsmtpsib-eac7683aa304c27d4161f8c0f24b8f8434b9f722ba129c8f16503897d0cc10c8-n0u1W23rs5BXMzB3"            # Aldığın SMTP Key

# Mail Detayları
sender_email = "office@umzugland.at"
receiver_email = "umzugasfsd@tutamail.com"          # Hedef adres
subject = "Anfrage über Dienstleistung"
body = "Sehr geehrte Damen und Herren,\n\nich interessiere mich für Ihre Dienstleistungen..."

msg = MIMEMultipart()
msg["From"] = sender_email
msg["To"] = receiver_email
msg["Subject"] = subject
msg.attach(MIMEText(body, "plain", "utf-8"))

try:
    server = smtplib.SMTP(smtp_server, smtp_port)
    server.starttls()
    server.login(smtp_user, smtp_pass)
    server.sendmail(sender_email, receiver_email, msg.as_string())
    server.quit()
    print("Mail office@umzugland.at üzerinden başarıyla gönderildi!")
except Exception as e:
    print(f"Hata oluştu: {e}")