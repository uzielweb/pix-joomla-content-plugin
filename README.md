# PIX - Joomla Content Plugin

[![Joomla Version](https://img.shields.io/badge/Joomla-4.x%20%7C%205.x%20%7C%206.x-blue.svg?style=flat-square)](https://joomla.org)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg?style=flat-square)](LICENSE)
[![Latest Release](https://img.shields.io/badge/release-1.0.4-orange.svg?style=flat-square)](https://github.com/uzielweb/plg_content_pix/releases)

A modern, standalone, and high-performance PIX payment shortcode integration for **Joomla 4, 5, and 6**. This plugin dynamically generates secure static PIX QR Codes directly inside your articles using a clean shortcode syntax.

[![Simulador & Demonstração](https://img.shields.io/badge/Simulador%20%26%20Demonstra%C3%A7%C3%A3o-Acesse%20Aqui-00b140?style=for-the-badge&logo=qrcode&logoColor=white)](https://uzielweb.github.io/plg_content_pix/)

---

## 🚀 Key Features

- **Local & Offline Generation**: No external APIs or remote servers are called to generate the QR Code. Everything is computed securely in the browser using JavaScript.
- **Strict Specifications Compliance**: Generates compliant BRCode payloads following the Banco Central do Brasil (BCB) standards.
- **Joomla Web Asset Manager**: Assets (JS/CSS) are cleanly registered and loaded using Joomla's modern asset architecture.
- **Copy-to-Clipboard**: Premium UI with an integrated "Copy PIX Code" button for better user experience.
- **Receipt Identifiers**: Allows attaching description messages directly into the bank payment receipt field (EMV Field 26-02).

---

## 📦 Installation

1. Download the latest release package from the [Releases](https://github.com/uzielweb/plg_content_pix/releases) page.
2. In your Joomla Administrator panel, navigate to **System > Install > Extensions**.
3. Upload the downloaded ZIP package.
4. Go to **System > Manage > Plugins**, search for **"PIX - Joomla Content Plugin"** (under the `content` group) and enable it.

---

## 🛠️ Configuration

Configure the global parameters inside the plugin manager:

| Parameter | Type | Required | Description |
| :--- | :--- | :---: | :--- |
| **PIX Key** | Text | **Yes** | Your PIX Key (E-mail, CPF, CNPJ, Phone, or Random Key). |
| **Merchant Name** | Text | No | Beneficiary name displayed to the buyer (Max 25 characters). |
| **Merchant City** | Text | No | The city where the transaction is processed (Max 15 characters). |

---

## 📖 How to Use

Insert the shortcode anywhere inside your Joomla articles or custom modules using the following syntax:

```text
{pix:CURRENCY|AMOUNT|MESSAGE}
```

### Examples:

- **Standard BRL Payment**:
  ```text
  {pix:BRL|890.00|Relogio Titanium}
  ```
- **USD equivalent or other text description**:
  ```text
  {pix:BRL|45.90|Inscricao Curso}
  ```

---

## 🎨 UI Customization

The plugin uses clean CSS classes which can be customized in your template style sheets:

- `.pontomega-pix-container` - Main widget wrapper.
- `.pontomega-pix-title` - Title text displaying the formatted amount.
- `.pontomega-pix-description` - Description/Message text.
- `.pontomega-pix-qrcode` - QR Code container.
- `.pontomega-pix-button` - Copy-to-clipboard button container.

---

## 📄 License

This project is licensed under the **GNU General Public License v2.0**.
