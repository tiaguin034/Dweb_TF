/* MedVet Connect - Integração com Backend */
const API_BASE = window.location.hostname.includes('vercel.app') ? '/api/' : 'backend/api/';

// Função para fazer requisições AJAX
function apiRequest(url, method = 'GET', data = null) {
    return $.ajax({
        url: API_BASE + url,
        method: method,
        data: data ? JSON.stringify(data) : null,
        contentType: 'application/json',
        dataType: 'json'
    });
}

// Verificar se usuário está logado
function verificarLogin() {
    return apiRequest('auth.php').then(response => {
        if(response.success && response.logged_in) {
            return response.user;
        }
        return null;
    }).catch(() => null);
}

// Função para mostrar mensagens
function mostrarMensagem(mensagem, tipo = 'info') {
    const alertClass = tipo === 'success' ? 'alert-success' : 
                      tipo === 'error' ? 'alert-danger' : 'alert-info';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').prepend(alert);
    setTimeout(() => alert.alert('close'), 5000);
}

$(function(){
  // Marcar link ativo da navbar conforme a página
  const path = location.pathname.split('/').pop();
  $('.navbar .nav-link').each(function(){
    const href = $(this).attr('href');
    if(href === path || (path === '' && href === 'index.html')){
      $(this).addClass('active');
    }
  });

  // Validação Bootstrap + jQuery
  $(document).on('submit', 'form[novalidate]', function(e){
    const form = this;
    if(!form.checkValidity()){
      e.preventDefault();
      e.stopPropagation();
    }
    $(form).addClass('was-validated');
  });

  // Login
  $('#formLogin').on('submit', function(e){
    if(!this.checkValidity()) return;
    e.preventDefault();
    
    const formData = {
      action: 'login',
      email: $(this).find('input[type="email"]').val(),
      senha: $(this).find('input[type="password"]').val()
    };
    
    apiRequest('auth.php', 'POST', formData).then(response => {
      if(response.success) {
        mostrarMensagem('Login realizado com sucesso!', 'success');
        setTimeout(() => {
          if(response.user.perfil === 'tutor') {
            window.location.href = 'tutor.html';
          } else {
            window.location.href = 'org.html';
          }
        }, 1000);
      } else {
        mostrarMensagem(response.message, 'error');
      }
    }).catch(() => {
      mostrarMensagem('Erro ao fazer login', 'error');
    });
  });

  // Cadastro
  $('#formCadastro').on('submit', function(e){
    if(!this.checkValidity()) return;
    e.preventDefault();
    
    const senha = $(this).find('input[type="password"]').first().val();
    const confirmarSenha = $(this).find('input[type="password"]').last().val();
    
    if(senha !== confirmarSenha) {
      mostrarMensagem('As senhas não coincidem', 'error');
      return;
    }
    
    const formData = {
      action: 'register',
      nome: $(this).find('input[type="text"]').val(),
      email: $(this).find('input[type="email"]').val(),
      senha: senha,
      perfil: $(this).find('select').val()
    };
    
    apiRequest('auth.php', 'POST', formData).then(response => {
      if(response.success) {
        mostrarMensagem('Cadastro realizado com sucesso!', 'success');
        setTimeout(() => {
          $('#login-tab').click();
        }, 1000);
      } else {
        mostrarMensagem(response.message, 'error');
      }
    }).catch(() => {
      mostrarMensagem('Erro ao cadastrar usuário', 'error');
    });
  });

  // Carregar animais para adoção
  function carregarAnimais() {
    const filtros = {};
    const busca = $('#buscaAdocao').val();
    const especie = $('#filtroEspecie').val();
    
    if(busca) filtros.busca = busca;
    if(especie) filtros.especie = especie;
    
    const params = new URLSearchParams(filtros);
    apiRequest('animais.php?' + params.toString()).then(response => {
      if(response.success) {
        renderizarAnimais(response.animais);
      }
    }).catch(() => {
      mostrarMensagem('Erro ao carregar animais', 'error');
    });
  }

  // Renderizar animais na página
  function renderizarAnimais(animais) {
    const container = $('#listaAdocao');
    if(container.length === 0) return;
    
    container.empty();
    
    animais.forEach(animal => {
      const card = $(`
        <div class="col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm item-adocao" data-especie="${animal.especie}" data-tags="${animal.cidade},${animal.tamanho || ''}">
            <img src="${animal.foto_url || 'https://placehold.co/600x400?text=' + encodeURIComponent(animal.nome)}" class="card-img-top" alt="${animal.nome}">
            <div class="card-body">
              <h5 class="card-title">${animal.nome}</h5>
              <p class="card-text small text-muted">${animal.especie} • ${animal.cidade} • ${animal.sexo}</p>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdocao" data-nome="${animal.nome}" data-id="${animal.id}">Adotar</button>
            </div>
          </div>
        </div>
      `);
      container.append(card);
    });
  }

  // Adoção: preencher nome do animal no modal
  $('#modalAdocao').on('show.bs.modal', function(ev){
    const btn = $(ev.relatedTarget);
    const nome = btn.data('nome') || '';
    const id = btn.data('id') || '';
    $('#nomeAnimal').val(nome);
    $('#animalId').val(id);
  });

  // Enviar solicitação de adoção
  $('#formAdocao').on('submit', function(e){
    e.preventDefault();
    
    const formData = {
      action: 'solicitar_adocao',
      animal_id: $('#animalId').val(),
      solicitante_nome: $(this).find('input[type="text"]').val(),
      solicitante_email: $(this).find('input[type="email"]').val(),
      solicitante_telefone: $(this).find('input[type="tel"]').val() || '',
      mensagem: $(this).find('textarea').val()
    };
    
    apiRequest('animais.php', 'POST', formData).then(response => {
      if(response.success) {
        mostrarMensagem('Solicitação enviada com sucesso!', 'success');
        $('#modalAdocao').modal('hide');
        this.reset();
        $(this).removeClass('was-validated');
      } else {
        mostrarMensagem(response.message, 'error');
      }
    }).catch(() => {
      mostrarMensagem('Erro ao enviar solicitação', 'error');
    });
  });

  // Filtro Adoção
  function filtrarAdocao(){
    carregarAnimais();
  }
  $('#buscaAdocao, #filtroEspecie').on('input change', filtrarAdocao);

  // Carregar animais na página de adoção
  if($('#listaAdocao').length > 0) {
    carregarAnimais();
  }

  // Carregar campanhas
  function carregarCampanhas() {
    const filtros = {};
    const busca = $('#buscaCampanhas').val();
    const data = $('#dataCampanhas').val();
    
    if(busca) filtros.busca = busca;
    if(data) filtros.data = data;
    
    const params = new URLSearchParams(filtros);
    apiRequest('campanhas.php?' + params.toString()).then(response => {
      if(response.success) {
        renderizarCampanhas(response.campanhas);
      }
    }).catch(() => {
      mostrarMensagem('Erro ao carregar campanhas', 'error');
    });
  }

  // Renderizar campanhas na página
  function renderizarCampanhas(campanhas) {
    const container = $('#listaCampanhas');
    if(container.length === 0) return;
    
    container.empty();
    
    campanhas.forEach(campanha => {
      const dataFormatada = new Date(campanha.data_evento).toLocaleDateString('pt-BR');
      const vagasRestantes = campanha.vagas_disponiveis - campanha.vagas_preenchidas;
      
      const card = $(`
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm item-campanha" data-tags="${campanha.cidade},${campanha.tipo}" data-data="${campanha.data_evento}">
            <div class="card-body">
              <h5 class="card-title">${campanha.nome}</h5>
              <p class="card-text small text-muted">${campanha.cidade} • ${dataFormatada}</p>
              <p>${campanha.descricao}</p>
              ${vagasRestantes > 0 ? `<p class="small text-success">${vagasRestantes} vagas disponíveis</p>` : '<p class="small text-warning">Vagas esgotadas</p>'}
              <a href="entrar.html" class="btn btn-outline-primary">Participar</a>
            </div>
          </div>
        </div>
      `);
      container.append(card);
    });
  }

  // Filtro Campanhas
  function filtrarCampanhas(){
    carregarCampanhas();
  }
  $('#buscaCampanhas, #dataCampanhas').on('input change', filtrarCampanhas);

  // Carregar campanhas na página de campanhas
  if($('#listaCampanhas').length > 0) {
    carregarCampanhas();
  }

  // Tutor: adicionar pet à lista (simulação local)
  $('#formPet').on('submit', function(e){
    if(!this.checkValidity()) return;
    e.preventDefault();
    const nome = $('#formPet input[type="text"]').first().val();
    const especie = $('#formPet select').val();
    const idade = $('#formPet input[type="number"]').val();
    const foto = $('#formPet input[type="url"]').val() || 'https://placehold.co/600x400?text=' + encodeURIComponent(nome);
    const card = `
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img src="${foto}" class="card-img-top" alt="${nome}">
          <div class="card-body">
            <h5 class="card-title">${nome}</h5>
            <p class="card-text small text-muted">${especie} • ${idade} anos</p>
            <button class="btn btn-outline-secondary btn-sm ver-historico" data-nome="${nome}">Ver histórico</button>
          </div>
        </div>
      </div>`;
    $('#listaPets').append(card);
    $('#modalPet').modal('hide');
    this.reset();
    $(this).removeClass('was-validated');
  });

  // ORG: criar campanha (simulação local)
  $('#formCampanha').on('submit', function(e){
    if(!this.checkValidity()) return;
    e.preventDefault();
    const nome = $('#formCampanha input[type="text"]').val();
    const cidade = $('#formCampanha input[type="text"]').eq(1).val();
    const data = $('#formCampanha input[type="date"]').val();
    $('#listaMinhasCampanhas').append(`<li class="list-group-item">${nome} • ${data} • ${cidade}</li>`);
    $('#modalCampanha').modal('hide');
    this.reset();
    $(this).removeClass('was-validated');
  });

  // ORG: publicar animal (simulação local -> adiciona em adoção.html se aberto)
  $('#formPublicarAnimal').on('submit', function(e){
    if(!this.checkValidity()) return;
    e.preventDefault();
    const nome = $('#formPublicarAnimal input[type="text"]').val();
    const especie = $('#formPublicarAnimal select').val();
    const foto = $('#formPublicarAnimal input[type="url"]').val() || 'https://placehold.co/600x400?text=' + encodeURIComponent(nome);
    const card = `
      <div class="col-sm-6 col-lg-4">
        <div class="card h-100 shadow-sm item-adocao" data-especie="${especie}" data-tags="">
          <img src="${foto}" class="card-img-top" alt="${nome}">
          <div class="card-body">
            <h5 class="card-title">${nome}</h5>
            <p class="card-text small text-muted">${especie}</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdocao" data-nome="${nome}">Adotar</button>
          </div>
        </div>
      </div>`;
    // Se a página de adoção estiver aberta em outra aba, o usuário pode atualizar; aqui adicionamos apenas na lista local se existir
    if($('#listaAdocao').length){ $('#listaAdocao').prepend(card); }
    $('#modalPublicarAnimal').modal('hide');
    this.reset();
    $(this).removeClass('was-validated');
  });
});
