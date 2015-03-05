db.games.remove({_id: 1});
db.games.insert({
  _id: 1,
  teams: [{name: '', score: 0, errors: 0}, {name: '', score: 0, errors: 0}],
  score: 0,
  currentQuestion: 0,
  questions: [
    {
      text: 'Куда по вечерам ходят девушки программисты?',
      answers: [
        {text: 'Домой', points: 27},
        {text: 'Бар/паб/пиво', points: 17},
        {text: 'Интернет', points: 11},
        {text: 'На кухню (в оффисе)', points: 9},
        {text: 'В git', points: 5},
        {text: 'В туалет', points: 2}
      ]
    },
    {
      text: 'Принтер зажевал бумагу, почему?',
      answers: [
        {text: 'Голоден', points: 37},
        {text: 'Сломан', points: 11},
        {text: 'Потому', points: 18},
        {text: 'Толстая бумага', points: 6},
        {text: 'Плохой/козел', points: 3},
        {text: 'YouTrack упал', points: 2},
      ]
    },
    {
      text: 'О чем можно говорить с девушкой в трамвае?',
      answers: [
        {text: 'О транспорте', points: 17},
        {text: 'О погоде', points: 18},
        {text: 'О вечном/смысле жизни', points: 13},
        {text: 'О билете', points: 10},
        {text: 'О книгах/поэзии', points: 5},
        {text: 'Вы выходите?', points: 4}
      ]
    },
    {
      text: 'На завтраке не было каши, почему?',
      answers: [
        {text: 'Съели', points: 28},
        {text: 'Сгорела', points: 6},
        {text: 'Мама уехала', points: 4},
        {text: 'Санкции/кризис', points: 7},
        {text: 'Это был обед', points: 4},
        {text: 'Проспал', points: 5}
      ]
    },
    {
      text: 'Зачем топтать мою любовь?',
      answers: [
        {text: 'Ее и так почти не стало', points: 24},
        {text: 'Просто/Затем', points: 19},
        {text: 'Смысловые галлюцинации', points: 5},
        {text: 'Чтобы стала плоской', points: 3},
        {text: 'В ответ', points: 3},
        {text: 'Скачать бесплатно', points: 1}
      ]
    }
  ]
});