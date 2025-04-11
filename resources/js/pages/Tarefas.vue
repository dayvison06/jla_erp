<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from 'vue';
import {
    PlusIcon,
    ClipboardListIcon,
    ClipboardIcon,
    ClockIcon,
    CheckCircleIcon,
    CalendarIcon,
    XIcon,
    BellIcon,
    SearchIcon,
    EditIcon,
    TrashIcon,
    CopyIcon,
    PaperclipIcon,
    LinkIcon,
    FileTextIcon,
    MessageSquareIcon,
    InboxIcon,
    LayoutIcon,
    BoldIcon,
    ItalicIcon,
    UnderlineIcon,
    ListIcon,
    ListOrderedIcon,
    AlignLeftIcon,
    AlignCenterIcon,
    AlignRightIcon,
    InfoIcon,
    AlertCircleIcon,
    AlertTriangleIcon,
    CheckIcon,
    ArrowRightIcon
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

// Dados de exemplo
const equipes = ref([
    { id: 1, nome: 'Desenvolvimento' },
    { id: 2, nome: 'Design' },
    { id: 3, nome: 'Marketing' },
    { id: 4, nome: 'Vendas' }
]);

const membros = ref([
    { id: 1, nome: 'Ana Silva', equipe: 1 },
    { id: 2, nome: 'Bruno Costa', equipe: 1 },
    { id: 3, nome: 'Carla Oliveira', equipe: 2 },
    { id: 4, nome: 'Daniel Santos', equipe: 2 },
    { id: 5, nome: 'Eduardo Lima', equipe: 3 },
    { id: 6, nome: 'Fernanda Martins', equipe: 4 }
]);

// Colunas do Kanban (agora dinâmicas)
const colunas = ref([
    { id: 'a_fazer', nome: 'A Fazer', cor: 'emerald' },
    { id: 'em_progresso', nome: 'Em Progresso', cor: 'blue' },
    { id: 'concluido', nome: 'Concluído', cor: 'purple' }
]);

const tarefas = ref([
    {
        id: 1,
        titulo: 'Implementar autenticação',
        descricao: '<p>Criar sistema de login e registro de usuários com validação de email.</p><ul><li>Implementar formulário de login</li><li>Adicionar validação de campos</li><li>Integrar com API de autenticação</li></ul>',
        status: 'a_fazer',
        prazo: '2023-12-15',
        responsavel: 1,
        equipe: 1,
        prioridade: 'alta',
        notificar: true,
        anexos: [
            { tipo: 'documento', nome: 'Especificação técnica', url: 'https://exemplo.com/doc.pdf', tamanho: 1024000 }
        ],
        comentarios: [
            { autor: 2, texto: 'Podemos usar JWT para autenticação?', data: '2023-12-01T10:30:00' }
        ]
    },
    {
        id: 2,
        titulo: 'Design da página inicial',
        descricao: '<p>Criar layout responsivo para a página inicial seguindo as diretrizes da marca.</p><p>O design deve ser compatível com dispositivos móveis e desktop.</p>',
        status: 'em_progresso',
        prazo: '2023-12-10',
        responsavel: 3,
        equipe: 2,
        prioridade: 'media',
        notificar: true,
        anexos: [
            { tipo: 'imagem', nome: 'Mockup inicial', url: 'https://via.placeholder.com/800x600', tamanho: 512000 }
        ],
        comentarios: []
    },
    {
        id: 3,
        titulo: 'Campanha de email marketing',
        descricao: '<p>Preparar conteúdo para campanha de fim de ano.</p><p>Incluir ofertas especiais e promoções sazonais.</p>',
        status: 'a_fazer',
        prazo: '2023-12-20',
        responsavel: 5,
        equipe: 3,
        prioridade: 'baixa',
        notificar: false,
        anexos: [],
        comentarios: []
    },
    {
        id: 4,
        titulo: 'Otimização de SEO',
        descricao: '<p>Melhorar o SEO do site para aumentar o tráfego orgânico.</p><p>Focar em palavras-chave relevantes e melhorar a estrutura de URLs.</p><p>Implementar meta tags e descrições otimizadas.</p>',
        status: 'concluido',
        prazo: '2023-12-05',
        responsavel: 5,
        equipe: 3,
        prioridade: 'media',
        notificar: false,
        anexos: [],
        comentarios: [
            { autor: 1, texto: 'Excelente trabalho! Já estamos vendo resultados.', data: '2023-12-06T14:15:00' }
        ]
    },
    {
        id: 5,
        titulo: 'Reunião com cliente',
        descricao: '<p>Apresentar proposta de projeto para o cliente potencial.</p><p>Preparar slides e demonstração do produto.</p>',
        status: 'em_progresso',
        prazo: '2023-12-08',
        responsavel: 6,
        equipe: 4,
        prioridade: 'alta',
        notificar: true,
        anexos: [],
        comentarios: []
    }
]);

// Estado dos filtros
const filtroEquipe = ref('');
const filtroMembro = ref('');
const filtroPrioridade = ref('');
const termoPesquisa = ref('');

// Estado do modal de tarefa
const modalTarefaVisivel = ref(false);
const tarefaAtual = ref({
    id: null,
    titulo: '',
    descricao: '',
    status: 'a_fazer',
    prazo: '',
    responsavel: '',
    equipe: '',
    prioridade: 'baixa',
    notificar: false,
    anexos: [],
    comentarios: []
});

// Estado do modal de coluna
const modalColunaVisivel = ref(false);
const colunaAtual = ref({
    id: '',
    nome: '',
    cor: 'gray'
});

// Estado do modal de anexo
const modalAnexoVisivel = ref(false);
const anexoAtual = ref({
    tipo: 'imagem',
    nome: '',
    url: '',
    tamanho: 0
});

// Estado do modal de link
const modalLinkVisivel = ref(false);
const linkAtual = ref({
    texto: '',
    url: ''
});

// Estado das notificações
const notificacoes = ref([]);
const notificacoesVisiveis = ref([]);

// Estado do novo comentário
const novoComentario = ref('');

// Estado do drag and drop
const isDragging = ref(null);
const isDraggingOver = ref(null);

// Referência ao editor de texto
const editorEl = ref(null);

// Botões do editor de texto
const editorButtons = [
    { icone: BoldIcon, comando: 'bold', titulo: 'Negrito' },
    { icone: ItalicIcon, comando: 'italic', titulo: 'Itálico' },
    { icone: UnderlineIcon, comando: 'underline', titulo: 'Sublinhado' },
    { icone: ListIcon, comando: 'insertUnorderedList', titulo: 'Lista não ordenada' },
    { icone: ListOrderedIcon, comando: 'insertOrderedList', titulo: 'Lista ordenada' },
    { icone: AlignLeftIcon, comando: 'justifyLeft', titulo: 'Alinhar à esquerda' },
    { icone: AlignCenterIcon, comando: 'justifyCenter', titulo: 'Centralizar' },
    { icone: AlignRightIcon, comando: 'justifyRight', titulo: 'Alinhar à direita' }
];

// Referência ao container do kanban
const kanbanContainer = ref(null);

// Tarefas filtradas com base nos filtros selecionados
const tarefasFiltradas = computed(() => {
    return tarefas.value.filter(tarefa => {
        const filtroEquipeMatch = !filtroEquipe.value || tarefa.equipe === parseInt(filtroEquipe.value);
        const filtroMembroMatch = !filtroMembro.value || tarefa.responsavel === parseInt(filtroMembro.value);
        const filtroPrioridadeMatch = !filtroPrioridade.value || tarefa.prioridade === filtroPrioridade.value;
        const termoPesquisaMatch = !termoPesquisa.value ||
            tarefa.titulo.toLowerCase().includes(termoPesquisa.value.toLowerCase()) ||
            (tarefa.descricao && stripHtml(tarefa.descricao).toLowerCase().includes(termoPesquisa.value.toLowerCase()));

        return filtroEquipeMatch && filtroMembroMatch && filtroPrioridadeMatch && termoPesquisaMatch;
    });
});

// Funções para manipulação de tarefas
function mostrarModalTarefa(tarefa = null, statusInicial = null) {
    if (tarefa) {
        tarefaAtual.value = JSON.parse(JSON.stringify(tarefa)); // Clone profundo
    } else {
        tarefaAtual.value = {
            id: null,
            titulo: '',
            descricao: '',
            status: statusInicial || 'a_fazer',
            prazo: new Date().toISOString().split('T')[0],
            responsavel: '',
            equipe: '',
            prioridade: 'baixa',
            notificar: false,
            anexos: [],
            comentarios: []
        };
    }
    modalTarefaVisivel.value = true;

    // Inicializar o editor após o DOM ser atualizado
    nextTick(() => {
        if (editorEl.value) {
            editorEl.value.innerHTML = tarefaAtual.value.descricao || '';
        }
    });
}

function fecharModalTarefa() {
    modalTarefaVisivel.value = false;
    novoComentario.value = '';
}

function salvarTarefa() {
    if (!tarefaAtual.value.titulo) {
        adicionarNotificacao('Erro', 'O título da tarefa é obrigatório', 'erro');
        return;
    }

    // Garantir que a descrição seja atualizada do editor
    if (editorEl.value) {
        tarefaAtual.value.descricao = editorEl.value.innerHTML;
    }

    if (tarefaAtual.value.id) {
        // Atualizar tarefa existente
        const index = tarefas.value.findIndex(t => t.id === tarefaAtual.value.id);
        if (index !== -1) {
            tarefas.value[index] = JSON.parse(JSON.stringify(tarefaAtual.value));
            adicionarNotificacao('Tarefa atualizada', 'As alterações foram salvas com sucesso', 'sucesso');
        }
    } else {
        // Adicionar nova tarefa
        const novoId = Math.max(0, ...tarefas.value.map(t => t.id)) + 1;
        const novaTarefa = {
            ...JSON.parse(JSON.stringify(tarefaAtual.value)),
            id: novoId
        };
        tarefas.value.push(novaTarefa);
        adicionarNotificacao('Tarefa criada', 'Nova tarefa adicionada com sucesso', 'sucesso');
    }

    fecharModalTarefa();
    verificarPrazos();
}

function excluirTarefa(id) {
    const tarefa = tarefas.value.find(t => t.id === id);
    if (tarefa) {
        tarefas.value = tarefas.value.filter(t => t.id !== id);
        adicionarNotificacao('Tarefa removida', `A tarefa "${tarefa.titulo}" foi removida`, 'info');
        fecharModalTarefa();
    }
}

function duplicarTarefa(tarefa) {
    const novaTarefa = JSON.parse(JSON.stringify(tarefa));
    novaTarefa.id = Math.max(0, ...tarefas.value.map(t => t.id)) + 1;
    novaTarefa.titulo = `${novaTarefa.titulo} (cópia)`;
    novaTarefa.comentarios = [];

    tarefas.value.push(novaTarefa);
    adicionarNotificacao('Tarefa duplicada', 'Uma cópia da tarefa foi criada', 'sucesso');
}

// Funções para manipulação de colunas
function mostrarModalColuna(coluna = null) {
    if (coluna) {
        colunaAtual.value = { ...coluna };
    } else {
        colunaAtual.value = {
            id: '',
            nome: '',
            cor: 'gray'
        };
    }
    modalColunaVisivel.value = true;
}

function fecharModalColuna() {
    modalColunaVisivel.value = false;
}

function salvarColuna() {
    if (!colunaAtual.value.nome) {
        adicionarNotificacao('Erro', 'O nome da coluna é obrigatório', 'erro');
        return;
    }

    if (!colunaAtual.value.id) {
        adicionarNotificacao('Erro', 'O ID da coluna é obrigatório', 'erro');
        return;
    }

    // Verificar se o ID já existe (exceto para a coluna atual)
    const idExistente = colunas.value.some(c => c.id === colunaAtual.value.id && c !== colunaAtual.value);
    if (idExistente) {
        adicionarNotificacao('Erro', 'Este ID de coluna já está em uso', 'erro');
        return;
    }

    if (colunaAtual.value.id.includes(' ')) {
        adicionarNotificacao('Erro', 'O ID da coluna não pode conter espaços', 'erro');
        return;
    }

    const index = colunas.value.findIndex(c => c.id === colunaAtual.value.id);

    if (index !== -1) {
        // Atualizar coluna existente
        colunas.value[index] = { ...colunaAtual.value };
        adicionarNotificacao('Coluna atualizada', 'As alterações foram salvas com sucesso', 'sucesso');
    } else {
        // Adicionar nova coluna
        colunas.value.push({ ...colunaAtual.value });
        adicionarNotificacao('Coluna criada', 'Nova coluna adicionada com sucesso', 'sucesso');
    }

    fecharModalColuna();
}

function excluirColuna() {
    // Verificar se há tarefas nesta coluna
    const tarefasNaColuna = tarefas.value.filter(t => t.status === colunaAtual.value.id);

    if (tarefasNaColuna.length > 0) {
        adicionarNotificacao('Erro', 'Não é possível excluir uma coluna que contém tarefas', 'erro');
        return;
    }

    colunas.value = colunas.value.filter(c => c.id !== colunaAtual.value.id);
    adicionarNotificacao('Coluna removida', `A coluna "${colunaAtual.value.nome}" foi removida`, 'info');
    fecharModalColuna();
}

// Funções para manipulação de anexos
function mostrarModalAnexo() {
    anexoAtual.value = {
        tipo: 'imagem',
        nome: '',
        url: '',
        tamanho: 0
    };
    modalAnexoVisivel.value = true;
}

function fecharModalAnexo() {
    modalAnexoVisivel.value = false;
}

function adicionarAnexo() {
    if (!anexoAtual.value.nome || !anexoAtual.value.url) {
        adicionarNotificacao('Erro', 'Nome e URL do anexo são obrigatórios', 'erro');
        return;
    }

    if (!tarefaAtual.value.anexos) {
        tarefaAtual.value.anexos = [];
    }

    tarefaAtual.value.anexos.push({
        ...anexoAtual.value,
        tamanho: anexoAtual.value.tamanho || 1024 * 1024 // 1MB padrão se não especificado
    });

    // Inserir referência ao anexo no editor
    if (editorEl.value) {
        const selecao = window.getSelection();
        const range = selecao.getRangeAt(0);

        let html = '';
        if (anexoAtual.value.tipo === 'imagem') {
            html = `<p><img src="${anexoAtual.value.url}" alt="${anexoAtual.value.nome}" style="max-width: 100%; max-height: 300px;"></p>`;
        } else {
            html = `<p><a href="${anexoAtual.value.url}" target="_blank">${anexoAtual.value.nome}</a></p>`;
        }

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const fragment = document.createDocumentFragment();
        while (tempDiv.firstChild) {
            fragment.appendChild(tempDiv.firstChild);
        }

        range.deleteContents();
        range.insertNode(fragment);

        // Atualizar a descrição
        tarefaAtual.value.descricao = editorEl.value.innerHTML;
    }

    fecharModalAnexo();
}

function removerAnexo(index) {
    tarefaAtual.value.anexos.splice(index, 1);
}

// Funções para manipulação de links
function mostrarModalLink() {
    linkAtual.value = {
        texto: '',
        url: ''
    };
    modalLinkVisivel.value = true;
}

function fecharModalLink() {
    modalLinkVisivel.value = false;
}

function adicionarLink() {
    if (!linkAtual.value.texto || !linkAtual.value.url) {
        adicionarNotificacao('Erro', 'Texto e URL do link são obrigatórios', 'erro');
        return;
    }

    // Inserir link no editor
    if (editorEl.value) {
        const selecao = window.getSelection();
        const range = selecao.getRangeAt(0);

        const linkElement = document.createElement('a');
        linkElement.href = linkAtual.value.url;
        linkElement.target = '_blank';
        linkElement.textContent = linkAtual.value.texto;

        range.deleteContents();
        range.insertNode(linkElement);

        // Atualizar a descrição
        tarefaAtual.value.descricao = editorEl.value.innerHTML;
    }

    fecharModalLink();
}

// Funções para manipulação de comentários
function adicionarComentario() {
    if (!novoComentario.value.trim()) return;

    if (!tarefaAtual.value.comentarios) {
        tarefaAtual.value.comentarios = [];
    }

    tarefaAtual.value.comentarios.push({
        autor: 1, // ID do usuário atual (simulado)
        texto: novoComentario.value,
        data: new Date().toISOString()
    });

    novoComentario.value = '';
}

// Funções para o editor de texto
function executarComandoEditor(comando) {
    document.execCommand(comando, false, null);
    if (editorEl.value) {
        tarefaAtual.value.descricao = editorEl.value.innerHTML;
    }
}

function atualizarDescricao(event) {
    tarefaAtual.value.descricao = event.target.innerHTML;
}

// Funções para drag and drop melhorado
function handleDragStart(event, tarefa) {
    isDragging.value = tarefa.id;
    event.dataTransfer.setData('tarefaId', tarefa.id);
    event.dataTransfer.effectAllowed = 'move';

    // Adicionar classe de estilo ao elemento arrastado
    event.target.classList.add('dragging');
}

function handleDragOver(event, colunaId) {
    event.preventDefault();
    isDraggingOver.value = colunaId;
    event.dataTransfer.dropEffect = 'move';
}

function handleDragLeave() {
    isDraggingOver.value = null;
}

function handleDrop(event, colunaId) {
    event.preventDefault();
    const tarefaId = parseInt(event.dataTransfer.getData('tarefaId'));
    const tarefa = tarefas.value.find(t => t.id === tarefaId);

    if (tarefa && tarefa.status !== colunaId) {
        tarefa.status = colunaId;
        adicionarNotificacao('Tarefa movida', `Tarefa movida para ${getNomeColuna(colunaId)}`, 'info');
    }

    isDraggingOver.value = null;
    isDragging.value = null;
}

function handleDragEnd() {
    isDragging.value = null;
    isDraggingOver.value = null;
}

// Funções auxiliares
function formatarData(data) {
    if (!data) return '';

    const dataObj = new Date(data);
    return dataObj.toLocaleDateString('pt-BR');
}

function formatarDataHora(data) {
    if (!data) return '';

    const dataObj = new Date(data);
    return `${dataObj.toLocaleDateString('pt-BR')} às ${dataObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
}

function getNomeMembro(membroId) {
    const membro = membros.value.find(m => m.id === parseInt(membroId));
    return membro ? membro.nome : 'Não atribuído';
}

function getIniciaisMembro(membroId) {
    const membro = membros.value.find(m => m.id === parseInt(membroId));
    if (!membro) return 'NA';

    const nomes = membro.nome.split(' ');
    if (nomes.length === 1) return nomes[0].charAt(0).toUpperCase();
    return (nomes[0].charAt(0) + nomes[nomes.length - 1].charAt(0)).toUpperCase();
}

function getResponsavelClass(membroId) {
    const cores = [
        'bg-emerald-100 text-emerald-800',
        'bg-blue-100 text-blue-800',
        'bg-purple-100 text-purple-800',
        'bg-pink-100 text-pink-800',
        'bg-yellow-100 text-yellow-800',
        'bg-orange-100 text-orange-800'
    ];

    if (!membroId) return 'bg-gray-100 text-gray-800';

    const index = (parseInt(membroId) - 1) % cores.length;
    return cores[index];
}

function getNomeColuna(colunaId) {
    const coluna = colunas.value.find(c => c.id === colunaId);
    return coluna ? coluna.nome : colunaId;
}

function getColumnCountClass(colunaId) {
    const baseClasses = 'bg-opacity-80';

    if (colunaId === 'a_fazer') return `bg-emerald-100 text-emerald-800 ${baseClasses}`;
    if (colunaId === 'em_progresso') return `bg-blue-100 text-blue-800 ${baseClasses}`;
    if (colunaId === 'concluido') return `bg-purple-100 text-purple-800 ${baseClasses}`;

    return `bg-gray-100 text-gray-800 ${baseClasses}`;
}

function getIconForColumn(colunaId) {
    if (colunaId === 'a_fazer') return ClipboardIcon;
    if (colunaId === 'em_progresso') return ClockIcon;
    if (colunaId === 'concluido') return CheckCircleIcon;

    return ClipboardListIcon;
}

function isPrazoProximo(data) {
    if (!data) return false;

    const hoje = new Date();
    const prazo = new Date(data);
    const diffDias = Math.ceil((prazo - hoje) / (1000 * 60 * 60 * 24));

    return diffDias >= 0 && diffDias <= 3;
}

function isPrazoVencido(data) {
    if (!data) return false;

    const hoje = new Date();
    const prazo = new Date(data);

    return prazo < hoje;
}

function diasRestantes(data) {
    if (!data) return 0;

    const hoje = new Date();
    const prazo = new Date(data);
    return Math.ceil((prazo - hoje) / (1000 * 60 * 60 * 24));
}

function formatarTamanhoArquivo(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function formatarDescricaoPreview(html) {
    // Limitar o HTML para preview
    return stripHtml(html).substring(0, 100) + (html.length > 100 ? '...' : '');
}

function stripHtml(html) {
    const temp = document.createElement('div');
    temp.innerHTML = html;
    return temp.textContent || temp.innerText || '';
}

// Sistema de notificações
function adicionarNotificacao(titulo, mensagem, tipo = 'info') {
    const notificacao = { titulo, mensagem, tipo, timestamp: new Date() };
    notificacoes.value.push(notificacao);
    notificacoesVisiveis.value.push(notificacao);

    // Remover notificação após 5 segundos
    setTimeout(() => {
        const index = notificacoesVisiveis.value.indexOf(notificacao);
        if (index !== -1) {
            notificacoesVisiveis.value.splice(index, 1);
        }
    }, 5000);
}

function removerNotificacao(index) {
    notificacoesVisiveis.value.splice(index, 1);
}

// Verificar prazos e enviar notificações
function verificarPrazos() {
    const hoje = new Date();

    tarefas.value.forEach(tarefa => {
        if (!tarefa.notificar || tarefa.status === 'concluido') return;

        const prazo = new Date(tarefa.prazo);
        const diffDias = Math.ceil((prazo - hoje) / (1000 * 60 * 60 * 24));

        if (diffDias === 1) {
            adicionarNotificacao('Lembrete de Prazo', `A tarefa "${tarefa.titulo}" vence amanhã!`, 'aviso');
        } else if (diffDias === 0) {
            adicionarNotificacao('Alerta de Prazo', `A tarefa "${tarefa.titulo}" vence hoje!`, 'aviso');
        } else if (diffDias < 0 && diffDias > -2) { // Apenas notificar se venceu recentemente
            adicionarNotificacao('Prazo Vencido', `A tarefa "${tarefa.titulo}" está atrasada!`, 'erro');
        }
    });
}

// Funções para badges de status minimalistas
function getStatusBadgeClass(tarefa) {
    // Prioridade alta com prazo vencido
    if (tarefa.prioridade === 'alta' && isPrazoVencido(tarefa.prazo)) {
        return 'bg-red-500 text-white';
    }

    // Prioridade alta com prazo próximo
    if (tarefa.prioridade === 'alta' && isPrazoProximo(tarefa.prazo)) {
        return 'bg-red-100 text-red-800';
    }

    // Prioridade média com prazo vencido
    if (tarefa.prioridade === 'media' && isPrazoVencido(tarefa.prazo)) {
        return 'bg-orange-500 text-white';
    }

    // Prioridade média com prazo próximo
    if (tarefa.prioridade === 'media' && isPrazoProximo(tarefa.prazo)) {
        return 'bg-yellow-100 text-yellow-800';
    }

    // Por prioridade
    if (tarefa.prioridade === 'alta') {
        return 'bg-red-50 text-red-700';
    }

    if (tarefa.prioridade === 'media') {
        return 'bg-yellow-50 text-yellow-700';
    }

    return 'bg-green-50 text-green-700';
}

function getStatusBadgeText(tarefa) {
    // Prazo vencido
    if (isPrazoVencido(tarefa.prazo)) {
        return 'Vencido';
    }

    // Prazo próximo
    if (isPrazoProximo(tarefa.prazo)) {
        const dias = diasRestantes(tarefa.prazo);
        return dias === 0 ? 'Hoje' : (dias === 1 ? 'Amanhã' : `${dias}d`);
    }

    // Por prioridade
    if (tarefa.prioridade === 'alta') {
        return 'Alta';
    }

    if (tarefa.prioridade === 'media') {
        return 'Média';
    }

    return 'Baixa';
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tarefas',
        href: '/tarefas',
    },
];

// Inicialização
onMounted(() => {
    verificarPrazos();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="min-h-screen p-4">
                <div class="max-w-full mx-auto">
                    <header class="mb-8">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                                Kanban
                            </h1>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="mostrarModalColuna()"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-all duration-200 flex items-center shadow-sm hover:shadow"
                                >
                                    <LayoutIcon class="w-4 h-4 mr-2" />
                                    Gerenciar Colunas
                                </button>
                                <button
                                    @click="mostrarModalTarefa()"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-all duration-200 flex items-center shadow-sm hover:shadow"
                                >
                                    <PlusIcon class="w-4 h-4 mr-2" />
                                    Nova Tarefa
                                </button>
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="bg-white rounded-xl shadow-sm p-4 transition-all duration-200 hover:shadow-md">
                            <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <div>
                                        <label for="filtroEquipe" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Equipe</label>
                                        <select
                                            id="filtroEquipe"
                                            v-model="filtroEquipe"
                                            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                        >
                                            <option value="">Todas as Equipes</option>
                                            <option v-for="equipe in equipes" :key="equipe.id" :value="equipe.id">{{ equipe.nome }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="filtroMembro" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Membro</label>
                                        <select
                                            id="filtroMembro"
                                            v-model="filtroMembro"
                                            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                        >
                                            <option value="">Todos os Membros</option>
                                            <option v-for="membro in membros" :key="membro.id" :value="membro.id">{{ membro.nome }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="filtroPrioridade" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Prioridade</label>
                                        <select
                                            id="filtroPrioridade"
                                            v-model="filtroPrioridade"
                                            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                                        >
                                            <option value="">Todas as Prioridades</option>
                                            <option value="alta">Alta</option>
                                            <option value="media">Média</option>
                                            <option value="baixa">Baixa</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="pesquisa" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="pesquisa"
                                            v-model="termoPesquisa"
                                            placeholder="Pesquisar tarefas..."
                                            class="w-full sm:w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        />
                                        <SearchIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Quadro Kanban -->
                    <div class="flex overflow-x-auto pb-4 gap-4 min-h-[calc(100vh-250px)]" ref="kanbanContainer">
                        <div
                            v-for="coluna in colunas"
                            :key="coluna.id"
                            class="bg-white rounded-xl shadow-sm min-w-[300px] w-[300px] flex flex-col max-h-full"
                        >
                            <div
                                class="p-4 border-b border-gray-100 rounded-t-xl flex items-center justify-between"
                                :class="{'bg-emerald-50': coluna.id === 'a_fazer', 'bg-blue-50': coluna.id === 'em_progresso', 'bg-purple-50': coluna.id === 'concluido', 'bg-gray-50': !['a_fazer', 'em_progresso', 'concluido'].includes(coluna.id)}"
                            >
                                <h2 class="font-semibold text-gray-800 flex items-center">
                                    <component :is="getIconForColumn(coluna.id)" class="w-5 h-5 mr-2 text-gray-600" />
                                    {{ coluna.nome }}
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium" :class="getColumnCountClass(coluna.id)">
                {{ tarefasFiltradas.filter(t => t.status === coluna.id).length }}
              </span>
                                </h2>
                                <div class="flex items-center">
                                    <button
                                        @click="mostrarModalColuna(coluna)"
                                        class="text-gray-400 hover:text-gray-600 p-1"
                                        title="Editar coluna"
                                    >
                                        <EditIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                            <div
                                class="p-3 flex-1 overflow-y-auto"
                                :class="{'drop-target': isDraggingOver === coluna.id}"
                                @dragover.prevent="handleDragOver($event, coluna.id)"
                                @dragleave="handleDragLeave"
                                @drop="handleDrop($event, coluna.id)"
                            >
                                <div
                                    v-for="tarefa in tarefasFiltradas.filter(t => t.status === coluna.id)"
                                    :key="tarefa.id"
                                    class="bg-white border border-gray-200 rounded-lg p-3 mb-3 shadow-sm hover:shadow transition-all duration-200 cursor-move group relative"
                                    :class="{'border-emerald-300 shadow-md': isDragging === tarefa.id}"
                                    draggable="true"
                                    @dragstart="handleDragStart($event, tarefa)"
                                    @dragend="handleDragEnd"
                                    @click.stop="mostrarModalTarefa(tarefa)"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-medium text-gray-800">{{ tarefa.titulo }}</h3>

                                        <!-- Badge minimalista para status -->
                                        <span
                                            class="text-xs px-1.5 py-0.5 rounded-sm font-medium"
                                            :class="getStatusBadgeClass(tarefa)"
                                        >
                  {{ getStatusBadgeText(tarefa) }}
                </span>
                                    </div>

                                    <div v-if="tarefa.anexos && tarefa.anexos.length > 0" class="mb-2">
                                        <div v-if="tarefa.anexos[0].tipo === 'imagem'" class="rounded-md overflow-hidden h-24 bg-gray-100">
                                            <img :src="tarefa.anexos[0].url" alt="Anexo" class="w-full h-full object-cover" />
                                        </div>
                                    </div>

                                    <div v-if="tarefa.descricao" class="text-sm text-gray-600 mb-3 line-clamp-2" v-html="formatarDescricaoPreview(tarefa.descricao)"></div>

                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-1 text-xs text-gray-500">
                                            <CalendarIcon class="w-3.5 h-3.5 mr-0.5" />
                                            {{ formatarData(tarefa.prazo) }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
                                                :class="getResponsavelClass(tarefa.responsavel)"
                                                :title="getNomeMembro(tarefa.responsavel)"
                                            >
                                                {{ getIniciaisMembro(tarefa.responsavel) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicadores minimalistas para anexos e comentários -->
                                    <div class="absolute top-3 right-10 flex gap-1">
                                        <div v-if="tarefa.anexos && tarefa.anexos.length > 0" class="text-gray-400" title="Possui anexos">
                                            <PaperclipIcon class="w-3.5 h-3.5" />
                                        </div>
                                        <div v-if="tarefa.comentarios && tarefa.comentarios.length > 0" class="text-gray-400" title="Possui comentários">
                                            <MessageSquareIcon class="w-3.5 h-3.5" />
                                        </div>
                                    </div>

                                    <!-- Menu de ações rápidas (visível apenas no hover) -->
                                    <div class="absolute top-2 right-2 hidden group-hover:flex bg-white shadow-md rounded-md border border-gray-200 p-1">
                                        <button
                                            @click.stop="mostrarModalTarefa(tarefa)"
                                            class="p-1 text-gray-500 hover:text-gray-700"
                                            title="Editar"
                                        >
                                            <EditIcon class="w-3.5 h-3.5" />
                                        </button>
                                        <button
                                            @click.stop="duplicarTarefa(tarefa)"
                                            class="p-1 text-gray-500 hover:text-gray-700"
                                            title="Duplicar"
                                        >
                                            <CopyIcon class="w-3.5 h-3.5" />
                                        </button>
                                        <button
                                            @click.stop="excluirTarefa(tarefa.id)"
                                            class="p-1 text-gray-500 hover:text-red-600"
                                            title="Excluir"
                                        >
                                            <TrashIcon class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </div>
                                <div
                                    v-if="tarefasFiltradas.filter(t => t.status === coluna.id).length === 0"
                                    class="text-center py-8 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-lg"
                                >
                                    <InboxIcon class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                                    Nenhuma tarefa
                                    <div class="mt-2">
                                        <button
                                            @click="mostrarModalTarefa(null, coluna.id)"
                                            class="text-emerald-600 hover:text-emerald-700 text-xs font-medium"
                                        >
                                            + Adicionar tarefa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botão para adicionar nova coluna -->
                        <div class="min-w-[200px] flex items-center justify-center">
                            <button
                                @click="mostrarModalColuna()"
                                class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors duration-200 flex items-center"
                            >
                                <PlusIcon class="w-5 h-5 mr-2" />
                                Adicionar Coluna
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal de Tarefa -->
                <div v-if="modalTarefaVisivel" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col" @click.stop>
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center">
            <span
                class="w-2 h-5 rounded-sm mr-3"
                :class="{
                'bg-red-500': tarefaAtual.prioridade === 'alta',
                'bg-yellow-500': tarefaAtual.prioridade === 'media',
                'bg-green-500': tarefaAtual.prioridade === 'baixa' || !tarefaAtual.prioridade
              }"
            ></span>
                                <h2 class="text-lg font-semibold text-gray-800">
                                    {{ tarefaAtual.id ? 'Editar Tarefa' : 'Nova Tarefa' }}
                                </h2>
                            </div>
                            <button @click="fecharModalTarefa" class="text-gray-500 hover:text-gray-700 p-1">
                                <XIcon class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="p-4 overflow-y-auto flex-1">
                            <div class="mb-4">
                                <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                <input
                                    type="text"
                                    id="titulo"
                                    v-model="tarefaAtual.titulo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Título da tarefa"
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                                <div class="border border-gray-300 rounded-lg shadow-sm overflow-hidden">
                                    <!-- Editor de texto rico -->
                                    <div class="bg-gray-50 border-b border-gray-200 p-1 flex flex-wrap gap-1">
                                        <button
                                            v-for="(btn, index) in editorButtons"
                                            :key="index"
                                            @click="executarComandoEditor(btn.comando)"
                                            class="p-1.5 rounded hover:bg-gray-200 text-gray-700"
                                            :title="btn.titulo"
                                        >
                                            <component :is="btn.icone" class="w-4 h-4" />
                                        </button>
                                        <div class="h-5 w-px bg-gray-300 mx-1"></div>
                                        <button
                                            @click="mostrarModalAnexo()"
                                            class="p-1.5 rounded hover:bg-gray-200 text-gray-700"
                                            title="Adicionar anexo"
                                        >
                                            <PaperclipIcon class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="mostrarModalLink()"
                                            class="p-1.5 rounded hover:bg-gray-200 text-gray-700"
                                            title="Adicionar link"
                                        >
                                            <LinkIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <div
                                        ref="editorEl"
                                        contenteditable="true"
                                        class="p-3 min-h-[150px] focus:outline-none"
                                        v-html="tarefaAtual.descricao || ''"
                                        @input="atualizarDescricao"
                                    ></div>
                                </div>
                            </div>

                            <!-- Anexos -->
                            <div v-if="tarefaAtual.anexos && tarefaAtual.anexos.length > 0" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Anexos</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div
                                        v-for="(anexo, index) in tarefaAtual.anexos"
                                        :key="index"
                                        class="border border-gray-200 rounded-lg p-2 flex items-center"
                                    >
                                        <div v-if="anexo.tipo === 'imagem'" class="w-12 h-12 bg-gray-100 rounded overflow-hidden mr-3">
                                            <img :src="anexo.url" alt="Anexo" class="w-full h-full object-cover" />
                                        </div>
                                        <div v-else class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center mr-3">
                                            <FileTextIcon class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-700 truncate">{{ anexo.nome }}</div>
                                            <div class="text-xs text-gray-500">{{ formatarTamanhoArquivo(anexo.tamanho) }}</div>
                                        </div>
                                        <button
                                            @click="removerAnexo(index)"
                                            class="p-1 text-gray-400 hover:text-red-500"
                                        >
                                            <XIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select
                                        id="status"
                                        v-model="tarefaAtual.status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option v-for="coluna in colunas" :key="coluna.id" :value="coluna.id">{{ coluna.nome }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="prioridade" class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
                                    <select
                                        id="prioridade"
                                        v-model="tarefaAtual.prioridade"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="baixa">Baixa</option>
                                        <option value="media">Média</option>
                                        <option value="alta">Alta</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="prazo" class="block text-sm font-medium text-gray-700 mb-1">Prazo</label>
                                    <input
                                        type="date"
                                        id="prazo"
                                        v-model="tarefaAtual.prazo"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    />
                                </div>
                                <div>
                                    <label for="responsavel" class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                                    <select
                                        id="responsavel"
                                        v-model="tarefaAtual.responsavel"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    >
                                        <option value="">Selecione um responsável</option>
                                        <option v-for="membro in membros" :key="membro.id" :value="membro.id">{{ membro.nome }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="equipe" class="block text-sm font-medium text-gray-700 mb-1">Equipe</label>
                                <select
                                    id="equipe"
                                    v-model="tarefaAtual.equipe"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="">Selecione uma equipe</option>
                                    <option v-for="equipe in equipes" :key="equipe.id" :value="equipe.id">{{ equipe.nome }}</option>
                                </select>
                            </div>

                            <div class="flex items-center mb-4">
                                <input
                                    type="checkbox"
                                    id="notificar"
                                    v-model="tarefaAtual.notificar"
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                />
                                <label for="notificar" class="ml-2 block text-sm text-gray-700">
                                    Enviar notificações sobre esta tarefa
                                </label>
                            </div>

                            <!-- Comentários -->
                            <div v-if="tarefaAtual.id" class="mt-6 border-t border-gray-200 pt-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <MessageSquareIcon class="w-4 h-4 mr-2" />
                                    Comentários
                                </h3>
                                <div v-if="tarefaAtual.comentarios && tarefaAtual.comentarios.length > 0" class="space-y-3 mb-4">
                                    <div
                                        v-for="(comentario, index) in tarefaAtual.comentarios"
                                        :key="index"
                                        class="bg-gray-50 rounded-lg p-3"
                                    >
                                        <div class="flex items-center mb-1">
                                            <div
                                                class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium mr-2"
                                                :class="getResponsavelClass(comentario.autor)"
                                            >
                                                {{ getIniciaisMembro(comentario.autor) }}
                                            </div>
                                            <div class="text-sm font-medium">{{ getNomeMembro(comentario.autor) }}</div>
                                            <div class="text-xs text-gray-500 ml-auto">{{ formatarDataHora(comentario.data) }}</div>
                                        </div>
                                        <div class="text-sm text-gray-700" v-html="comentario.texto"></div>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500 mb-3 text-center py-3">
                                    Nenhum comentário ainda
                                </div>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        v-model="novoComentario"
                                        placeholder="Adicionar comentário..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                        @keyup.enter="adicionarComentario"
                                    />
                                    <button
                                        @click="adicionarComentario"
                                        class="px-3 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                                        :disabled="!novoComentario.trim()"
                                    >
                                        Enviar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-between">
                            <button
                                v-if="tarefaAtual.id"
                                @click="excluirTarefa(tarefaAtual.id)"
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                            >
                                Excluir
                            </button>
                            <div class="ml-auto">
                                <button
                                    @click="fecharModalTarefa"
                                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors mr-2"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="salvarTarefa"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Coluna -->
                <div v-if="modalColunaVisivel" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md" @click.stop>
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-800">
                                    {{ colunaAtual.id ? 'Editar Coluna' : 'Nova Coluna' }}
                                </h2>
                                <button @click="fecharModalColuna" class="text-gray-500 hover:text-gray-700">
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <label for="nomeColuna" class="block text-sm font-medium text-gray-700 mb-1">Nome da Coluna</label>
                                <input
                                    type="text"
                                    id="nomeColuna"
                                    v-model="colunaAtual.nome"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Ex: Em Revisão"
                                />
                            </div>

                            <div class="mb-4">
                                <label for="idColuna" class="block text-sm font-medium text-gray-700 mb-1">ID da Coluna</label>
                                <input
                                    type="text"
                                    id="idColuna"
                                    v-model="colunaAtual.id"
                                    :disabled="colunaAtual.id && ['a_fazer', 'em_progresso', 'concluido'].includes(colunaAtual.id)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Ex: em_revisao"
                                />
                                <p v-if="colunaAtual.id && ['a_fazer', 'em_progresso', 'concluido'].includes(colunaAtual.id)" class="text-xs text-gray-500 mt-1">
                                    O ID das colunas padrão não pode ser alterado.
                                </p>
                            </div>

                            <div class="mb-4">
                                <label for="corColuna" class="block text-sm font-medium text-gray-700 mb-1">Cor da Coluna</label>
                                <select
                                    id="corColuna"
                                    v-model="colunaAtual.cor"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emeral-500 focus:border-emerald-500"
                                >
                                    <option value="emerald">Verde</option>
                                    <option value="blue">Azul</option>
                                    <option value="purple">Roxo</option>
                                    <option value="pink">Rosa</option>
                                    <option value="yellow">Amarelo</option>
                                    <option value="orange">Laranja</option>
                                    <option value="gray">Cinza</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-between">
                            <button
                                v-if="colunaAtual.id && !['a_fazer', 'em_progresso', 'concluido'].includes(colunaAtual.id)"
                                @click="excluirColuna"
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                            >
                                Excluir
                            </button>
                            <div :class="{'ml-auto': colunaAtual.id && !['a_fazer', 'em_progresso', 'concluido'].includes(colunaAtual.id)}">
                                <button
                                    @click="fecharModalColuna"
                                    class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors mr-2"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="salvarColuna"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                                >
                                    Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Anexo -->
                <div v-if="modalAnexoVisivel" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md" @click.stop>
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-800">Adicionar Anexo</h2>
                                <button @click="fecharModalAnexo" class="text-gray-500 hover:text-gray-700">
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <label for="tipoAnexo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Anexo</label>
                                <select
                                    id="tipoAnexo"
                                    v-model="anexoAtual.tipo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                                    <option value="imagem">Imagem</option>
                                    <option value="documento">Documento</option>
                                    <option value="link">Link</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="nomeAnexo" class="block text-sm font-medium text-gray-700 mb-1">Nome do Anexo</label>
                                <input
                                    type="text"
                                    id="nomeAnexo"
                                    v-model="anexoAtual.nome"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Ex: Documento de requisitos"
                                />
                            </div>

                            <div class="mb-4">
                                <label for="urlAnexo" class="block text-sm font-medium text-gray-700 mb-1">URL do Anexo</label>
                                <input
                                    type="text"
                                    id="urlAnexo"
                                    v-model="anexoAtual.url"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="https://exemplo.com/imagem.jpg"
                                />
                            </div>

                            <div v-if="anexoAtual.tipo === 'imagem' && anexoAtual.url" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pré-visualização</label>
                                <div class="border border-gray-200 rounded-lg p-2 bg-gray-50">
                                    <img :src="anexoAtual.url" alt="Pré-visualização" class="max-h-40 mx-auto object-contain" />
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button
                                @click="fecharModalAnexo"
                                class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors mr-2"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="adicionarAnexo"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                            >
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal de Link -->
                <div v-if="modalLinkVisivel" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md" @click.stop>
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-800">Adicionar Link</h2>
                                <button @click="fecharModalLink" class="text-gray-500 hover:text-gray-700">
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="mb-4">
                                <label for="textoLink" class="block text-sm font-medium text-gray-700 mb-1">Texto do Link</label>
                                <input
                                    type="text"
                                    id="textoLink"
                                    v-model="linkAtual.texto"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="Ex: Documentação do projeto"
                                />
                            </div>

                            <div class="mb-4">
                                <label for="urlLink" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                                <input
                                    type="text"
                                    id="urlLink"
                                    v-model="linkAtual.url"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                    placeholder="https://exemplo.com"
                                />
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button
                                @click="fecharModalLink"
                                class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors mr-2"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="adicionarLink"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
                            >
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notificações -->
                <div class="fixed bottom-4 right-4 z-40">
                    <div
                        v-for="(notificacao, index) in notificacoesVisiveis"
                        :key="index"
                        class="bg-white rounded-lg shadow-lg p-4 mb-2 w-80 animate-slide-in flex"
                        :class="{
          'border-l-4 border-emerald-500': notificacao.tipo === 'sucesso',
          'border-l-4 border-blue-500': notificacao.tipo === 'info',
          'border-l-4 border-yellow-500': notificacao.tipo === 'aviso',
          'border-l-4 border-red-500': notificacao.tipo === 'erro'
        }"
                    >
                        <div class="mr-3">
                            <CheckCircleIcon v-if="notificacao.tipo === 'sucesso'" class="w-5 h-5 text-emerald-500" />
                            <InfoIcon v-else-if="notificacao.tipo === 'info'" class="w-5 h-5 text-blue-500" />
                            <AlertCircleIcon v-else-if="notificacao.tipo === 'aviso'" class="w-5 h-5 text-yellow-500" />
                            <AlertTriangleIcon v-else-if="notificacao.tipo === 'erro'" class="w-5 h-5 text-red-500" />
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800">{{ notificacao.titulo }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ notificacao.mensagem }}</p>
                        </div>
                        <button @click="removerNotificacao(index)" class="text-gray-400 hover:text-gray-600 self-start">
                            <XIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-slide-in {
    animation: slideIn 0.3s ease-out forwards;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.drop-target {
    background-color: rgba(236, 253, 245, 0.5);
    border: 2px dashed #10b981;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.dragging {
    opacity: 0.6;
    transform: scale(0.98);
    transition: all 0.2s ease;
}

/* Estilização para o editor de texto */
[contenteditable] {
    outline: none;
}

[contenteditable] img {
    max-width: 100%;
    height: auto;
    margin: 8px 0;
}

[contenteditable] a {
    color: #10b981;
    text-decoration: underline;
}

[contenteditable] ul,
[contenteditable] ol {
    padding-left: 1.5rem;
}

[contenteditable] ul li {
    list-style-type: disc;
}

[contenteditable] ol li {
    list-style-type: decimal;
}

/* Estilização para o container do kanban */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
