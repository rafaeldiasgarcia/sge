<?php
#
# Serviço de Envio de E-mails.
# Centraliza a lógica de envio de e-mails para autenticação e notificações.
# Utiliza PHPMailer para enviar e-mails via SMTP.
#
namespace Application\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;
    private $isConfigured;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->isConfigured = $this->configureSMTP();
    }

    /**
     * Configura o servidor SMTP para envio de e-mails
     */
    private function configureSMTP(): bool
    {
        try {
            // Configurações do servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host       = 'smtp.gmail.com';
            $this->mailer->SMTPAuth   = true;
            
            // ⚠️ IMPORTANTE: Configure estas variáveis com suas credenciais
            $this->mailer->Username   = getenv('SMTP_EMAIL') ?: 'rafdgar@gmail.com';
            $this->mailer->Password   = getenv('SMTP_PASSWORD') ?: 'tkbd pkkh yyqj fipq';
            
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = 587;
            $this->mailer->CharSet    = 'UTF-8';

            // Configurações do remetente
            $fromEmail = 'rafdgar@gmail.com';
            $this->mailer->setFrom($fromEmail, 'SGE UNIFIO');

            return true;
        } catch (Exception $e) {
            error_log("Erro ao configurar SMTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia e-mail com código de verificação para login (2FA)
     */
    public function sendVerificationCode(string $toEmail, string $toName, string $code): bool
    {
        if (!$this->isConfigured) {
            error_log("EmailService: SMTP não configurado. Pulando envio de e-mail.");
            return false;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Seu código de verificação - SGE UNIFIO';
            
            $this->mailer->Body = $this->getVerificationCodeTemplate($toName, $code);
            $this->mailer->AltBody = "Seu código de verificação é: {$code}\n\nEste código expira em 5 minutos.";

            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email de verificação enviado com sucesso para: {$toEmail}");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Erro ao enviar email de verificação: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Envia e-mail com link de recuperação de senha
     */
    public function sendPasswordRecoveryLink(string $toEmail, string $toName, string $token): bool
    {
        if (!$this->isConfigured) {
            error_log("EmailService: SMTP não configurado. Pulando envio de e-mail.");
            return false;
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);

            // Gerar URL completa do site
            $baseUrl = $this->getBaseUrl();
            $recoveryLink = $baseUrl . "/redefinir-senha?token=" . $token;

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recuperação de Senha - SGE UNIFIO';
            
            $this->mailer->Body = $this->getPasswordRecoveryTemplate($toName, $recoveryLink);
            $this->mailer->AltBody = "Recuperação de Senha\n\nClique no link abaixo para redefinir sua senha:\n{$recoveryLink}\n\nEste link expira em 1 hora.";

            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email de recuperação enviado com sucesso para: {$toEmail}");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Erro ao enviar email de recuperação: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Obtém a URL base do site
     */
    private function getBaseUrl(): string
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            return $protocol . '://' . $_SERVER['HTTP_HOST'];
        }
        
        // Fallback para desenvolvimento local
        return 'http://localhost';
    }

    /**
     * Template HTML para e-mail de código de verificação
     */
    private function getVerificationCodeTemplate(string $toName, string $code): string
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                    <tr>
                        <td align='center'>
                            <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                <tr>
                                    <td style='background-color: #0056b3; padding: 30px; text-align: center;'>
                                        <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>SGE UNIFIO</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 40px 30px;'>
                                        <h2 style='color: #0056b3; margin-top: 0; font-size: 24px;'>Código de Verificação</h2>
                                        <p style='color: #333333; font-size: 16px; line-height: 1.6;'>Olá, <strong>" . htmlspecialchars($toName) . "</strong>!</p>
                                        <p style='color: #333333; font-size: 16px; line-height: 1.6;'>Seu código de verificação para acessar o SGE UNIFIO é:</p>
                                        <div style='background-color: #f8f9fa; border: 2px solid #0056b3; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;'>
                                            <span style='font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #0056b3;'>{$code}</span>
                                        </div>
                                        <p style='color: #666666; font-size: 14px; line-height: 1.6;'>
                                            <strong>⏱️ Este código expira em 15 minutos.</strong>
                                        </p>
                                        <p style='color: #999999; font-size: 13px; line-height: 1.6; margin-top: 30px;'>
                                            Se você não solicitou este código, por favor ignore este e-mail. Sua conta permanece segura.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='background-color: #f8f9fa; padding: 20px 30px; border-top: 1px solid #e9ecef;'>
                                        <p style='color: #999999; font-size: 12px; margin: 0; text-align: center;'>
                                            SGE UNIFIO - Sistema de Gerenciamento de Eventos<br>
                                            Este é um e-mail automático, por favor não responda.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        ";
    }

    /**
     * Template HTML para e-mail de recuperação de senha
     */
    private function getPasswordRecoveryTemplate(string $toName, string $recoveryLink): string
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
                <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                    <tr>
                        <td align='center'>
                            <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                                <tr>
                                    <td style='background-color: #0056b3; padding: 30px; text-align: center;'>
                                        <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>SGE UNIFIO</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 40px 30px;'>
                                        <h2 style='color: #0056b3; margin-top: 0; font-size: 24px;'>Recuperação de Senha</h2>
                                        <p style='color: #333333; font-size: 16px; line-height: 1.6;'>Olá, <strong>" . htmlspecialchars($toName) . "</strong>!</p>
                                        <p style='color: #333333; font-size: 16px; line-height: 1.6;'>
                                            Você solicitou a recuperação de senha para sua conta no SGE UNIFIO.
                                        </p>
                                        <p style='color: #333333; font-size: 16px; line-height: 1.6;'>
                                            Clique no botão abaixo para redefinir sua senha:
                                        </p>
                                        <div style='text-align: center; margin: 35px 0;'>
                                            <a href='{$recoveryLink}' 
                                               style='background-color: #0056b3; color: #ffffff; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px; font-weight: bold;'>
                                                Redefinir Senha
                                            </a>
                                        </div>
                                        <p style='color: #666666; font-size: 13px; line-height: 1.6;'>
                                            Ou copie e cole este link no seu navegador:
                                        </p>
                                        <p style='background-color: #f8f9fa; padding: 15px; word-break: break-all; font-size: 12px; color: #0056b3; border-radius: 5px;'>
                                            {$recoveryLink}
                                        </p>
                                        <p style='color: #666666; font-size: 14px; line-height: 1.6; margin-top: 30px;'>
                                            <strong>⏱️ Este link expira em 1 hora.</strong>
                                        </p>
                                        <p style='color: #999999; font-size: 13px; line-height: 1.6; margin-top: 30px;'>
                                            Se você não solicitou esta recuperação de senha, por favor ignore este e-mail. Sua senha permanecerá inalterada.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='background-color: #f8f9fa; padding: 20px 30px; border-top: 1px solid #e9ecef;'>
                                        <p style='color: #999999; font-size: 12px; margin: 0; text-align: center;'>
                                            SGE UNIFIO - Sistema de Gerenciamento de Eventos<br>
                                            Este é um e-mail automático, por favor não responda.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        ";
    }
}