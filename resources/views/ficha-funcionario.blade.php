<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Completa do Funcionário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #e5e7eb;
            -webkit-print-color-adjust: exact;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm; /* Margem um pouco menor para caber tudo */
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Classes auxiliares para Labels e Valores */
        .label {
            display: block;
            font-size: 0.65rem; /* Text-xs um pouco menor */
            text-transform: uppercase;
            font-weight: 700;
            color: #6b7280; /* Gray-500 */
            margin-bottom: 2px;
        }
        .value {
            display: block;
            font-size: 0.875rem; /* Text-sm */
            font-weight: 500;
            color: #111827; /* Gray-900 */
            min-height: 1.25rem; /* Garante altura mesmo vazio */
            border-bottom: 1px dotted #d1d5db; /* Linha pontilhada sutil para guiar leitura */
            padding-bottom: 1px;
        }

        @media print {
            body { background: none; margin: 0; }
            .page { margin: 0; box-shadow: none; width: 100%; page-break-after: always; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print text-center p-4">
    <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-900 transition font-semibold">
        Imprimir Ficha / Salvar PDF
    </button>
</div>

<div class="page">

    <header class="flex gap-6 mb-6 border-b-2 border-gray-800 pb-6">
        <div class="w-28 h-28 bg-gray-200 rounded border border-gray-300 overflow-hidden flex-shrink-0">
            <img src="https://ui-avatars.com/api/?name=João+Silva&background=cbd5e1&size=128" alt="Foto" class="w-full h-full object-cover">
        </div>

        <div class="flex-1 flex flex-col justify-center">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 uppercase">João da Silva</h1>
                    <p class="text-sm text-gray-500 font-medium">JLA Engenharia</p>
                </div>
                <div class="text-right">
                    <span class="block text-xs uppercase font-bold text-gray-500">Código / Matrícula</span>
                    <span class="text-3xl font-bold text-gray-800">004592</span>
                </div>
            </div>

            <div class="mt-4 flex gap-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        ATIVO (Status)
                    </span>
                <span class="text-sm text-gray-600 flex items-center gap-1">
                        <strong>Admissão:</strong> 10/01/2023
                    </span>
            </div>
        </div>
    </header>

    <div class="space-y-5">

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Dados Pessoais
            </h3>
            <div class="grid grid-cols-4 gap-x-4 gap-y-3">
                <div class="col-span-2">
                    <span class="label">Nome Completo</span>
                    <span class="value">João da Silva</span>
                </div>
                <div>
                    <span class="label">Data de Nascimento</span>
                    <span class="value">15/05/1990</span>
                </div>
                <div>
                    <span class="label">Gênero</span>
                    <span class="value">Masculino</span>
                </div>

                <div>
                    <span class="label">Estado Civil</span>
                    <span class="value">Casado</span>
                </div>
                <div>
                    <span class="label">Nacionalidade</span>
                    <span class="value">Brasileira</span>
                </div>
                <div class="col-span-2">
                    <span class="label">Naturalidade</span>
                    <span class="value">São Paulo - SP</span>
                </div>

                <div class="col-span-2">
                    <span class="label">Nome da Mãe</span>
                    <span class="value">Maria da Silva</span>
                </div>
                <div class="col-span-2">
                    <span class="label">Nome do Pai</span>
                    <span class="value">José da Silva</span>
                </div>

                <div class="col-span-2">
                    <span class="label">Grau de Instrução</span>
                    <span class="value">Ensino Superior Completo</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Documentação
            </h3>
            <div class="grid grid-cols-5 gap-x-4 gap-y-3">
                <div>
                    <span class="label">CPF</span>
                    <span class="value">123.456.789-00</span>
                </div>
                <div>
                    <span class="label">RG</span>
                    <span class="value">33.444.555-X</span>
                </div>
                <div>
                    <span class="label">Órgão Emissor</span>
                    <span class="value">SSP/SP</span>
                </div>
                <div>
                    <span class="label">Data Emissão</span>
                    <span class="value">10/02/2010</span>
                </div>
                <div>
                    <span class="label">CNPJ (Se PJ)</span>
                    <span class="value">-</span>
                </div>

                <div>
                    <span class="label">Título de Eleitor</span>
                    <span class="value">1234 5678 0101</span>
                </div>
                <div>
                    <span class="label">Cert. Reservista</span>
                    <span class="value">123456</span>
                </div>
                <div>
                    <span class="label">CNH</span>
                    <span class="value">999999999</span>
                </div>
                <div>
                    <span class="label">Categoria CNH</span>
                    <span class="value">AB</span>
                </div>
                <div>
                    <span class="label">Validade CNH</span>
                    <span class="value">10/10/2026</span>
                </div>

                <div class="col-span-2">
                    <span class="label">Registro Profissional (CREA/OAB...)</span>
                    <span class="value">123456/ES</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Endereço e Contato
            </h3>
            <div class="grid grid-cols-4 gap-x-4 gap-y-3">
                <div>
                    <span class="label">CEP</span>
                    <span class="value">01001-000</span>
                </div>
                <div class="col-span-2">
                    <span class="label">Rua / Logradouro</span>
                    <span class="value">Avenida Paulista</span>
                </div>
                <div>
                    <span class="label">Número</span>
                    <span class="value">1000</span>
                </div>

                <div>
                    <span class="label">Complemento</span>
                    <span class="value">Apto 50</span>
                </div>
                <div>
                    <span class="label">Bairro</span>
                    <span class="value">Bela Vista</span>
                </div>
                <div>
                    <span class="label">Cidade</span>
                    <span class="value">São Paulo</span>
                </div>
                <div>
                    <span class="label">UF</span>
                    <span class="value">SP</span>
                </div>

                <div>
                    <span class="label">Telefone Fixo</span>
                    <span class="value">(11) 3333-4444</span>
                </div>
                <div>
                    <span class="label">Celular / WhatsApp</span>
                    <span class="value">(11) 99999-8888</span>
                </div>
                <div class="col-span-2">
                    <span class="label">E-mail</span>
                    <span class="value">joao.silva@email.com</span>
                </div>

                <div class="col-span-2">
                    <span class="label">Contato de Emergência</span>
                    <span class="value">Maria (Esposa)</span>
                </div>
                <div class="col-span-2">
                    <span class="label">Telefone Emergência</span>
                    <span class="value">(11) 98888-7777</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Dados Contratuais
            </h3>
            <div class="grid grid-cols-4 gap-x-4 gap-y-3">
                <div>
                    <span class="label">Tipo de Contrato</span>
                    <span class="value">CLT</span>
                </div>
                <div>
                    <span class="label">Data Admissão</span>
                    <span class="value">10/01/2023</span>
                </div>
                <div>
                    <span class="label">Data Rescisão</span>
                    <span class="value">-</span>
                </div>
                <div>
                    <span class="label">Salário Base</span>
                    <span class="value">R$ 5.000,00</span>
                </div>

                <div>
                    <span class="label">CTPS (Número)</span>
                    <span class="value">1234567</span>
                </div>
                <div>
                    <span class="label">CTPS (Série)</span>
                    <span class="value">0001</span>
                </div>
                <div>
                    <span class="label">CTPS (UF)</span>
                    <span class="value">SP</span>
                </div>
                <div>
                    <span class="label">PIS / PASEP</span>
                    <span class="value">123.45678.90-0</span>
                </div>
                <div>
                    <span class="label">NIT</span>
                    <span class="value">123.45678.90-0</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Dados Bancários
            </h3>
            <div class="grid grid-cols-4 gap-x-4 gap-y-3">
                <div>
                    <span class="label">Banco</span>
                    <span class="value">001 - Banco do Brasil</span>
                </div>
                <div>
                    <span class="label">Agência</span>
                    <span class="value">1234-5</span>
                </div>
                <div>
                    <span class="label">Conta</span>
                    <span class="value">998877-6</span>
                </div>
                <div>
                    <span class="label">Tipo de Conta</span>
                    <span class="value">Corrente</span>
                </div>
                <div>
                    <span class="label">Tipo Chave PIX</span>
                    <span class="value">CPF</span>
                </div>
                <div class="col-span-3">
                    <span class="label">Chave PIX</span>
                    <span class="value">123.456.789-00</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Saúde e Segurança (SSO)
            </h3>
            <div class="grid grid-cols-4 gap-x-4 gap-y-3">
                <div>
                    <span class="label">Tipo Sanguíneo</span>
                    <span class="value">O+</span>
                </div>
                <div>
                    <span class="label">Último Exame (ASO)</span>
                    <span class="value">10/01/2024</span>
                </div>
                <div>
                    <span class="label">Próximo Exame</span>
                    <span class="value">10/01/2025</span>
                </div>
                <div>
                    <span class="label">Resultado ASO</span>
                    <span class="value">Apto</span>
                </div>

                <div class="col-span-2">
                    <span class="label text-red-600">Alergias</span>
                    <span class="value text-red-700 font-bold">Nenhuma relatada</span>
                </div>
                <div class="col-span-2">
                    <span class="label">Histórico de Acidentes</span>
                    <span class="value">Nenhum registro</span>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-bold bg-gray-100 border-l-4 border-gray-800 px-2 py-1 mb-3 text-gray-800 uppercase">
                Informações Adicionais
            </h3>
            <div class="border border-gray-300 rounded p-2 min-h-[60px] text-sm text-gray-700">
                <p>Funcionário possui especialização em operação de máquinas pesadas. Disponibilidade para viagens.</p>
            </div>
        </section>

    </div> <footer class="mt-8 pt-6 border-t border-gray-400 break-inside-avoid">
        <div class="flex justify-between gap-10">
            <div class="flex-1 text-center">
                <div class="border-b border-black mb-2 h-8"></div>
                <p class="text-xs font-bold uppercase">Assinatura do Funcionário</p>
            </div>
            <div class="flex-1 text-center">
                <div class="border-b border-black mb-2 h-8"></div>
                <p class="text-xs font-bold uppercase">Assinatura RH / Responsável</p>
            </div>
        </div>
        <p class="text-center text-[10px] text-gray-400 mt-4">Documento gerado automaticamente em <span id="data-impressao"></span></p>
    </footer>

</div>

<script>
    document.getElementById('data-impressao').innerText = new Date().toLocaleString('pt-BR');
</script>
</body>
</html>
