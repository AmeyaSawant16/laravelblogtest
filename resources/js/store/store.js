import { defineStore } from 'pinia';

export const useStore = defineStore('store', {
    state: () => ({
        searchResults: [],
    }),

    actions: {
        setSearchResults(result) {
            this.searchResults = result;
        },

        addPost(post) {
            this.posts.push(post);
        }
    },

    getters: {
        getSearchResult(){
            return this.searchResults;
        }
    }


});
