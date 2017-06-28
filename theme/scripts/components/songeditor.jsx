import createReactComponent from './createReactComponent';
import React from 'react';
import ReactDOM from 'react-dom';
import {Editor, EditorState} from 'draft-js';

class SongEditor extends React.Component {
  state = {editorState: EditorState.createEmpty()}
  onChange = (editorState) => this.setState({editorState})

  render() {
    return (
      <Editor editorState={this.state.editorState} onChange={this.onChange} />
    )
  }
}

module.exports = createReactComponent(SongEditor)