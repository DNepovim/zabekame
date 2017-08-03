import createReactComponent from './createReactComponent';
import React, { Component } from 'react';

class SongEditor extends React.Component {

  handleKeyPress = (event) => {
    console.log(event)
  }
  render() {
    return (
      <div>
        <textarea className="song-input song-text song-lyric" onKeyPress={this.handleKeyPress}></textarea>
      </div>
    );
  }
}

module.exports = createReactComponent(SongEditor)
