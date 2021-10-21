
var server = require('http').createServer(function(req,res){res.write("welcome");res.end();});

var io = require('socket.io')(server);
io = io.of('/socketLink')
io.on('connection', function(socket) {
      socket.emit("connected");
//Entete JOIN WarningQuizEleve
      socket.on('join_class', function(class_id) {
          socket.join("class_"+class_id);
          socket.room="class_"+class_id;
      });
//DECK_EDIT EVENT
      socket.on('join_edit', function(deck_id,fn) {
          socket.join("deck_"+deck_id);
          socket.room="deck_"+deck_id;
          fn("join edit deck done");
      });
      socket.on('cardUpdate', function(data) {
          socket.broadcast.in(socket.room).emit('cardUpdate',data);
      });
      socket.on('cardDelete', function(card_id) {
          socket.broadcast.in(socket.room).emit('cardDelete',card_id);
      });
      socket.on('deckUpdate', function(data) {
          socket.broadcast.in(socket.room).emit('deckUpdate',data);
      });
      socket.on('GCUpdate', function(data) {
          socket.broadcast.in(socket.room).emit('GCUpdate',data);
      });
      socket.on('changeEditorWrite', function() {
          socket.broadcast.in(socket.room).emit('changeEditorWrite');
      });

//BOX EVENT
    // once a client has connected, we expect to get a ping from them saying what room they want to join
    socket.on('newBox', function(data) {
        io.in(socket.room).emit('newBox',data);
    });
    socket.on('updateBoxName', function(data) {
        io.in(socket.room).emit('updateBoxName',data);
    });
    socket.on('removeBox', function(data) {
        io.in(socket.room).emit('removeBox',data);
    });
    socket.on('checkBox', function(data) {
        io.in(socket.room).emit('checkBox',data);
    });
    socket.on('startBox', function(data) {
        io.in(socket.room).emit('startBox',data);
    });
    socket.on('correctionBack', function(data) {
        io.in(socket.room).emit('correctionBack',data);
    });


    socket.on('game', function(quiz_id,fn) {
        socket.join(quiz_id);
        socket.room=quiz_id;
        fn("game_done");
    });
//QUIZ EVENT

    socket.on('newQuizforClass', function(class_id) {
        io.in("class_"+class_id).emit('newQuizForYou');
    });
    socket.on('closeQuizforClass', function(class_id) {
        io.in("class_"+class_id).emit('closeQuizForYou');
    });

    socket.on('newQuizOpen', function() {
        io.emit('newQuizOpen');
    });
    socket.on('answeredReceived', function(data) {
        io.in(socket.room).emit('answeredReceived',data);
    });

    socket.on('whoIsThere', function(quiz_id) {
        io.in(socket.room).emit('whoIsThere');
    });

    socket.on('playerJoin', function(data) {
      socket.user_id = data.user_id;
      io.in(socket.room).emit('playerJoin',data.user_id);
    });

    socket.on('state', function(result){
        io.in(socket.room).emit('state',result);
    });


    socket.on('start', function(result){
      io.in(socket.room).emit('start',result);
    });

    socket.on('playerAway', function(result){
      io.in(socket.room).emit('playerAway',result);
    });

    socket.on('playerInAgain', function(result){
      io.in(socket.room).emit('playerInAgain',result);
    });

    socket.on('pause', function(){
      io.in(socket.room).emit('pause');
    });

    socket.on('fin', function(){
      io.in(socket.room).emit('fin');
    });

    socket.on('answer', function(myAnswer){
      io.in(socket.room).emit('oneAnswer',{answer:myAnswer,user_id:socket.user_id});
    });


    socket.on('goodUser', function(data){
      io.in(socket.room).emit('goodUser',{data});
    });

    socket.on('victory', function(victoryArray){
      io.in(socket.room).emit('victory',victoryArray);
    });

    socket.on('playerLeave', function(data) {
      io.in(data.quiz_id).emit('playerLeave',data);
      socket.leave(socket.room);
    });

    socket.on('nbrePlayer', function(quiz_id,fn) {
               if(nbrePlayerInRoom=io.adapter.rooms[quiz_id])
                 {nbrePlayerInRoom=io.adapter.rooms[quiz_id].length;}
               else{nbrePlayerInRoom=0;}
               //nbrePlayerInRoom=100;
               fn(nbrePlayerInRoom)
    }); //fin query

    socket.on('disconnect', function(){
      data={user_id:socket.user_id, quiz_id:socket.room};
      io.in(socket.room).emit('playerLeave',data);
      socket.leave(socket.room);
    });
});

//lancement du serveur
server.listen(3030);
