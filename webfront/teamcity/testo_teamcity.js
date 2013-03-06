var config= require( './testo_config.js' )

var log= function( message ){
    require( 'fs' ).appendFileSync( 'testo_server.log', message + '\n' )
}

require( 'child_process' ).spawn
(   'lh_server.cmd'
,   [ ]
,   { detached: true, cwd: '..' }
)

var server= require( 'child_process' ).spawn
(   'testo_server.cmd'
,   [ ]
,   { detached: true }
)

setTimeout( run, config.timeout )

function run(){

    var socket = require( 'socket.io-client' ).connect( '//' + config.host + ':' + config.port )
    
    socket.on( 'connect', function( ){
        console.log( "##teamcity[testSuiteStarted name='browser.unittest']" )
        
        console.log( "##teamcity[message text='gogogo!']" )
        
        setTimeout( function(){
            console.log( "##teamcity[message text='timeout']" )
        }, 30000 )
        
        socket.on( 'test:done', function( states ){
            console.log( "##teamcity[message text='count: " + Object.keys( states ).length + "']" )
            
            for( var agent in states ){
                agent= agent.replace( /'/g, "|'" ).replace( /\n/g, "|n" ).replace( /\r/g, "|r" ).replace( /\|/g, "||" ).replace( /\]/g, "|]" )
                console.log( "##teamcity[testStarted name='" + agent[0] + "']" )
                
                if( !states[ agent ] ) console.log( "##teamcity[testFailed  name='" + agent[0] + "']" )
                
                console.log( "##teamcity[testFinished name='" + agent[0] + "']" )
                log( "##teamcity[testFinished name='" + agent[0] + "']" )
            }
            
            console.log( "##teamcity[testSuiteFinished name='browser.unittest']" )
            
            socket.disconnect()
            process.exit(1)
        } )
        
        socket.on( 'disconnect', function( ){
            console.log( "##teamcity[message text='WTF?!?']" )
        })
        
        socket.emit( 'test:run' )
        
        console.log( "##teamcity[message text='emited!']" )
        
    })

}