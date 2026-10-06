{{-- Passkey (WebAuthn) istemci yardımcıları: window.Passkey.register(name) / window.Passkey.login() --}}
@once
<script>
    window.Passkey = (() => {
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;
        const b64uToBuf = (s) => {
            s = s.replace(/-/g, '+').replace(/_/g, '/'); s += '='.repeat((4 - s.length % 4) % 4);
            return Uint8Array.from(atob(s), c => c.charCodeAt(0)).buffer;
        };
        const bufToB64u = (b) => btoa(String.fromCharCode(...new Uint8Array(b))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        const post = async (url, body) => {
            const r = await fetch(url, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify(body || {}),
            });
            const data = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error(data.message || 'İşlem başarısız (HTTP ' + r.status + ')');
            return data;
        };
        // Tarayıcı penceresini kullanıcı kapattıysa sessiz geç
        const friendly = (e) => (e && (e.name === 'NotAllowedError' || e.name === 'AbortError'))
            ? new Error('Passkey işlemi iptal edildi ya da süresi doldu.') : e;

        return {
            supported: () => !!(window.PublicKeyCredential && navigator.credentials && window.isSecureContext),

            async register(name) {
                const args = await post(@js(route('passkeys.register.options')));
                const pk = args.publicKey;
                pk.challenge = b64uToBuf(pk.challenge);
                pk.user.id = b64uToBuf(pk.user.id);
                (pk.excludeCredentials || []).forEach(c => c.id = b64uToBuf(c.id));
                let cred;
                try { cred = await navigator.credentials.create({ publicKey: pk }); } catch (e) { throw friendly(e); }
                return post(@js(route('passkeys.register')), {
                    name: name || null,
                    clientDataJSON: bufToB64u(cred.response.clientDataJSON),
                    attestationObject: bufToB64u(cred.response.attestationObject),
                });
            },

            async login() {
                const args = await post(@js(route('passkeys.login.options')));
                const pk = args.publicKey;
                pk.challenge = b64uToBuf(pk.challenge);
                (pk.allowCredentials || []).forEach(c => c.id = b64uToBuf(c.id));
                let cred;
                try { cred = await navigator.credentials.get({ publicKey: pk }); } catch (e) { throw friendly(e); }
                const res = await post(@js(route('passkeys.login')), {
                    id: bufToB64u(cred.rawId),
                    clientDataJSON: bufToB64u(cred.response.clientDataJSON),
                    authenticatorData: bufToB64u(cred.response.authenticatorData),
                    signature: bufToB64u(cred.response.signature),
                    userHandle: cred.response.userHandle ? bufToB64u(cred.response.userHandle) : null,
                });
                window.location.href = res.redirect || '/';
                return res;
            },
        };
    })();
</script>
@endonce
