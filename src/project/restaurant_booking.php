<div class="modal p-0" id="prenotazioneModal" tabindex="-1" aria-labelledby="prenotazioneModalLabel"   
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header text-white rounded-top-2" style="background-color: #0f2541;">
                <h5 class="modal-title" id="prenotazioneModalLabel">Effettua una Prenotazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Chiudi"></button>
            </div>

            <div class="modal-body p-4">
                <form action="actions/add_prenotazione.php" method="POST" id="prenotazioneForm">
                    <div class="row w-100">
                        <!-- Colonna sinistra (più alta, 4 colonne) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_prenotazione" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data_prenotazione" name="data" required>
                            </div>
                            <div class="mb-3">
                                <label for="ora_prenotazione" class="form-label">Ora</label>
                                <input type="time" class="form-control" id="ora_prenotazione" name="ora" required>
                            </div>
                            <div class="mb-3">
                                <label for="num_persone" class="form-label">Numero Persone</label>
                                <input type="number" class="form-control" id="num_persone" name="num_persone" min="1"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Telefono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9+ ]+"
                                    required>
                            </div>
                        </div>

                        <!-- Colonna destra (più stretta, 2 colonne) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="cognome" class="form-label">Cognome</label>
                                <input type="text" class="form-control" id="cognome" name="cognome" required>
                            </div>
                            <img src="../assets/images/logo.png" alt="Tavolo Ristorante" class="restaurant-image">

                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill">Prenota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>