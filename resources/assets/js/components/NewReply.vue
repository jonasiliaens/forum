<template>
	<div>
		<div v-if="signedIn">
			<div class="form-group">
				<textarea 	name="body" 
						id="body" 
						class="form-control" 
						placeholder="Have something to say?" 
						rows="5"
						required 
						v-model="body"></textarea>
			</div>
				
			<div class="form-group">
				<button type="submit" 
						class="btn btn-primary"
						@click="addReply">Verzenden</button>
			</div>
		</div>

		<p class="text-center" v-else>
			Please <a href="/login">sign in</a> to post a reply.
		</p>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				body: ''
			};
		},

		computed: {
			signedIn() {
				return window.App.signedIn;
			}
		},

		methods: {
			addReply() {
				axios.post(location.pathname + '/replies', { body: this.body })
					.then(({data}) => {
						this.body = '';

						flash('Your reply has been posted');

						this.$emit('created', data);
					});
			}
		}
	}
</script>