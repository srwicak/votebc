// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract EVotingSystem {
    struct Vote {
        uint256 electionId;
        uint256 candidateId;
        address voter;
        uint256 timestamp;
        bytes32 voteHash;
    }

    mapping(uint256 => mapping(address => bool)) public hasVoted;
    mapping(uint256 => mapping(uint256 => uint256)) public voteCounts;
    mapping(uint256 => uint256) public totalVotes; // sesuai ABI: public mapping
    mapping(uint256 => Vote[]) private electionVotes;

    event VoteCast(
        uint256 indexed electionId,
        uint256 indexed candidateId,
        address indexed voter,
        uint256 timestamp,
        bytes32 voteHash
    );

    /**
     * @dev Memberikan suara dalam pemilihan
     */
    function castVote(
        uint256 _electionId,
        uint256 _candidateId,
        bytes32 _voterIdHash
    ) public {
        require(!hasVoted[_electionId][msg.sender], "Already voted in this election");

        hasVoted[_electionId][msg.sender] = true;
        voteCounts[_electionId][_candidateId]++;
        totalVotes[_electionId]++;

        bytes32 voteHash = keccak256(
            abi.encodePacked(
                _electionId,
                _candidateId,
                msg.sender,
                block.timestamp,
                _voterIdHash
            )
        );

        electionVotes[_electionId].push(
            Vote({
                electionId: _electionId,
                candidateId: _candidateId,
                voter: msg.sender,
                timestamp: block.timestamp,
                voteHash: voteHash
            })
        );

        emit VoteCast(_electionId, _candidateId, msg.sender, block.timestamp, voteHash);
    }

    /**
     * @dev Mendapatkan jumlah suara untuk kandidat
     */
    function getVoteCount(
        uint256 _electionId,
        uint256 _candidateId
    ) public view returns (uint256) {
        return voteCounts[_electionId][_candidateId];
    }

    /**
     * @dev Verifikasi suara
     */
    function verifyVote(
        bytes32 _voteHash,
        uint256 _electionId,
        uint256 _candidateId,
        address _voter,
        uint256 _timestamp,
        bytes32 _voterIdHash
    ) public pure returns (bool) {
        bytes32 computedHash = keccak256(
            abi.encodePacked(
                _electionId,
                _candidateId,
                _voter,
                _timestamp,
                _voterIdHash
            )
        );
        return computedHash == _voteHash;
    }
}
