// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * @title EVotingSystem
 * @dev Smart contract untuk sistem e-voting dengan blockchain
 */
contract EVotingSystem {
    address public admin;
    
    struct Election {
        uint256 id;
        string title;
        uint256 startTime;
        uint256 endTime;
        bool isActive;
        address creator;
    }
    
    struct Candidate {
        uint256 id;
        uint256 electionId;
        string name;
        string details;
    }
    
    struct Vote {
        uint256 electionId;
        uint256 candidateId;
        address voter;
        uint256 timestamp;
        bytes32 voteHash;
    }
    
    mapping(uint256 => Election) public elections;
    mapping(uint256 => Candidate) public candidates;
    mapping(uint256 => mapping(address => bool)) public hasVoted;
    mapping(uint256 => mapping(uint256 => uint256)) public voteCounts;
    mapping(uint256 => uint256) public totalVotes;
    mapping(uint256 => Vote[]) private electionVotes;
    
    uint256 public electionCount;
    uint256 public candidateCount;
    
    event ElectionCreated(uint256 indexed electionId, string title, uint256 startTime, uint256 endTime, address creator);
    event CandidateAdded(uint256 indexed candidateId, uint256 indexed electionId, string name);
    event VoteCast(uint256 indexed electionId, uint256 indexed candidateId, address indexed voter, uint256 timestamp, bytes32 voteHash);
    event ElectionStatusChanged(uint256 indexed electionId, bool isActive);
    
    modifier onlyAdmin() {
        require(msg.sender == admin, "Only admin can call this function");
        _;
    }
    
    modifier electionExists(uint256 _electionId) {
        require(elections[_electionId].id == _electionId, "Election does not exist");
        _;
    }
    
    modifier candidateExists(uint256 _candidateId) {
        require(candidates[_candidateId].id == _candidateId, "Candidate does not exist");
        _;
    }
    
    modifier electionActive(uint256 _electionId) {
        require(elections[_electionId].isActive, "Election is not active");
        require(block.timestamp >= elections[_electionId].startTime, "Election has not started yet");
        require(block.timestamp <= elections[_electionId].endTime, "Election has ended");
        _;
    }
    
    constructor() {
        admin = msg.sender;
    }
    
    /**
     * @dev Membuat pemilihan baru
     * @param _title Judul pemilihan
     * @param _startTime Waktu mulai pemilihan (UNIX timestamp)
     * @param _endTime Waktu berakhir pemilihan (UNIX timestamp)
     */
    function createElection(string memory _title, uint256 _startTime, uint256 _endTime) public {
        require(_startTime < _endTime, "End time must be after start time");
        
        electionCount++;
        uint256 electionId = electionCount;
        
        elections[electionId] = Election({
            id: electionId,
            title: _title,
            startTime: _startTime,
            endTime: _endTime,
            isActive: true,
            creator: msg.sender
        });
        
        emit ElectionCreated(electionId, _title, _startTime, _endTime, msg.sender);
    }
    
    /**
     * @dev Menambahkan kandidat ke pemilihan
     * @param _electionId ID pemilihan
     * @param _name Nama kandidat
     * @param _details Detail kandidat (dapat berupa JSON string)
     */
    function addCandidate(uint256 _electionId, string memory _name, string memory _details) 
        public 
        electionExists(_electionId)
    {
        require(msg.sender == elections[_electionId].creator || msg.sender == admin, "Only election creator or admin can add candidates");
        
        candidateCount++;
        uint256 candidateId = candidateCount;
        
        candidates[candidateId] = Candidate({
            id: candidateId,
            electionId: _electionId,
            name: _name,
            details: _details
        });
        
        emit CandidateAdded(candidateId, _electionId, _name);
    }
    
    /**
     * @dev Memberikan suara dalam pemilihan
     * @param _electionId ID pemilihan
     * @param _candidateId ID kandidat
     * @param _voterIdHash Hash dari ID pemilih (untuk privasi)
     */
    function castVote(uint256 _electionId, uint256 _candidateId, bytes32 _voterIdHash) 
        public 
        electionExists(_electionId)
        candidateExists(_candidateId)
        electionActive(_electionId)
    {
        require(candidates[_candidateId].electionId == _electionId, "Candidate not in this election");
        require(!hasVoted[_electionId][msg.sender], "Already voted in this election");
        
        hasVoted[_electionId][msg.sender] = true;
        voteCounts[_electionId][_candidateId]++;
        totalVotes[_electionId]++;
        
        // Generate vote hash for verification
        bytes32 voteHash = keccak256(abi.encodePacked(_electionId, _candidateId, msg.sender, block.timestamp, _voterIdHash));
        
        Vote memory newVote = Vote({
            electionId: _electionId,
            candidateId: _candidateId,
            voter: msg.sender,
            timestamp: block.timestamp,
            voteHash: voteHash
        });
        
        electionVotes[_electionId].push(newVote);
        
        emit VoteCast(_electionId, _candidateId, msg.sender, block.timestamp, voteHash);
    }
    
    /**
     * @dev Mengubah status aktif pemilihan
     * @param _electionId ID pemilihan
     * @param _isActive Status aktif baru
     */
    function setElectionStatus(uint256 _electionId, bool _isActive) 
        public 
        onlyAdmin 
        electionExists(_electionId)
    {
        elections[_electionId].isActive = _isActive;
        emit ElectionStatusChanged(_electionId, _isActive);
    }
    
    /**
     * @dev Mendapatkan jumlah suara untuk kandidat
     * @param _electionId ID pemilihan
     * @param _candidateId ID kandidat
     * @return Jumlah suara
     */
    function getVoteCount(uint256 _electionId, uint256 _candidateId) 
        public 
        view 
        electionExists(_electionId)
        candidateExists(_candidateId)
        returns (uint256)
    {
        return voteCounts[_electionId][_candidateId];
    }
    
    /**
     * @dev Memeriksa apakah pemilih sudah memberikan suara
     * @param _electionId ID pemilihan
     * @param _voter Alamat pemilih
     * @return Boolean yang menunjukkan apakah pemilih sudah memberikan suara
     */
    function hasUserVoted(uint256 _electionId, address _voter) 
        public 
        view 
        electionExists(_electionId)
        returns (bool)
    {
        return hasVoted[_electionId][_voter];
    }
    
    /**
     * @dev Mendapatkan detail pemilihan
     * @param _electionId ID pemilihan
     * @return id ID pemilihan
     * @return title Judul pemilihan
     * @return startTime Waktu mulai
     * @return endTime Waktu berakhir
     * @return isActive Status aktif
     * @return creator Alamat pembuat
     */
    function getElectionDetails(uint256 _electionId) 
        public 
        view 
        electionExists(_electionId)
        returns (uint256 id, string memory title, uint256 startTime, uint256 endTime, bool isActive, address creator)
    {
        Election memory election = elections[_electionId];
        return (election.id, election.title, election.startTime, election.endTime, election.isActive, election.creator);
    }
    
    /**
     * @dev Verifikasi suara
     * @param _voteHash Hash suara yang akan diverifikasi
     * @param _electionId ID pemilihan
     * @param _candidateId ID kandidat
     * @param _voter Alamat pemilih
     * @param _timestamp Waktu voting
     * @param _voterIdHash Hash ID pemilih
     * @return Boolean yang menunjukkan apakah suara valid
     */
    function verifyVote(bytes32 _voteHash, uint256 _electionId, uint256 _candidateId, address _voter, uint256 _timestamp, bytes32 _voterIdHash) 
        public 
        pure 
        returns (bool)
    {
        bytes32 computedHash = keccak256(abi.encodePacked(_electionId, _candidateId, _voter, _timestamp, _voterIdHash));
        return computedHash == _voteHash;
    }
}