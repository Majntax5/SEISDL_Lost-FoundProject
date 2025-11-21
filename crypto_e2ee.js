// crypto_e2ee.js
// Utilities for ECDH P-256 + AES-GCM encryption
async function base64ToArrayBuffer(b64) {
  return Uint8Array.from(atob(b64), c=>c.charCodeAt(0)).buffer;
}
async function arrayBufferToBase64(buf) {
  const bytes = new Uint8Array(buf);
  let binary = '';
  for (let i=0;i<bytes.byteLength;i++) binary += String.fromCharCode(bytes[i]);
  return btoa(binary);
}

// Export/import JWK for ECDH keys
async function generateKeyPair() {
  const kp = await crypto.subtle.generateKey(
    { name: 'ECDH', namedCurve: 'P-256' },
    true,
    ['deriveKey']
  );
  const pub = await crypto.subtle.exportKey('jwk', kp.publicKey);
  const priv = await crypto.subtle.exportKey('jwk', kp.privateKey);
  return { publicJwk: pub, privateJwk: priv };
}

async function importPublicKeyJwk(jwk) {
  return crypto.subtle.importKey('jwk', jwk, { name: 'ECDH', namedCurve: 'P-256' }, true, []);
}
async function importPrivateKeyJwk(jwk) {
  return crypto.subtle.importKey('jwk', jwk, { name: 'ECDH', namedCurve: 'P-256' }, true, ['deriveKey']);
}

async function deriveSharedKey(privateKey, otherPublicKey) {
  // privateKey: CryptoKey (ECDH deriveKey allowed)
  // otherPublicKey: CryptoKey
  const derived = await crypto.subtle.deriveKey(
    { name: 'ECDH', public: otherPublicKey },
    privateKey,
    { name: 'AES-GCM', length: 256 },
    false,
    ['encrypt','decrypt']
  );
  return derived;
}

async function encryptForRecipient(privateJwk, recipientPublicJwk, plaintext) {
  const priv = await importPrivateKeyJwk(privateJwk);
  const otherPub = await importPublicKeyJwk(recipientPublicJwk);
  const aesKey = await deriveSharedKey(priv, otherPub);
  const enc = new TextEncoder().encode(plaintext);
  const iv = crypto.getRandomValues(new Uint8Array(12));
  const ciphertext = await crypto.subtle.encrypt({ name:'AES-GCM', iv }, aesKey, enc);
  return {
    ciphertext: await arrayBufferToBase64(ciphertext),
    iv: await arrayBufferToBase64(iv.buffer)
  };
}

async function decryptFromSender(privateJwk, senderPublicJwk, ciphertextB64, ivB64) {
  const priv = await importPrivateKeyJwk(privateJwk);
  const otherPub = await importPublicKeyJwk(senderPublicJwk);
  const aesKey = await deriveSharedKey(priv, otherPub);
  const cipherBuf = await base64ToArrayBuffer(ciphertextB64);
  const ivBuf = await base64ToArrayBuffer(ivB64);
  const plainBuf = await crypto.subtle.decrypt({ name:'AES-GCM', iv: new Uint8Array(ivBuf) }, aesKey, cipherBuf);
  return new TextDecoder().decode(plainBuf);
}
