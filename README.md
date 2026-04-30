# Joomla PIX Content Plugin (plg_content_pix)

A modern, standalone PIX payment integration for Joomla 4 and 5. This plugin allows you to easily render PIX QR codes within your articles using a simple shortcode syntax.

## Features

- **Local Generation**: No external APIs used for QR code generation. Everything happens in the browser via JavaScript.
- **Strict Compliance**: Generates PIX payloads following the BCB (Banco Central do Brasil) specifications (EMV BRCode).
- **Customizable**: Set your PIX key, merchant name, and city in the plugin parameters.
- **Receipt Integration**: Includes transaction messages in the bank receipt (Field 26-02).
- **Responsive Design**: Premium UI with "Copy PIX Code" functionality included.

## Installation

1. Download the repository as a ZIP.
2. In your Joomla Administrator, go to **System > Install > Extensions**.
3. Upload the ZIP file.
4. Go to **System > Manage > Plugins**, search for "Content - Pix" and enable it.

## Usage

Insert the following shortcode anywhere in your articles:

```
{pix:MOEDA|VALOR|MENSAGEM}
```

Example:
```
{pix:BRL|890.00|Relogio Titanium}
```

## Configuration

In the plugin settings, configure:
- **PIX Key**: Your email, phone, CPF/CNPJ, or random key.
- **Merchant Name**: Your name or your company's name.
- **Merchant City**: Your city.

## Credits

- **Author**: Uziel Almeida Oliveira
- **Contact**: contato@pontomega.com.br
- **Website**: [www.pontomega.com.br](http://www.pontomega.com.br)

## License

GNU General Public License v2.0
